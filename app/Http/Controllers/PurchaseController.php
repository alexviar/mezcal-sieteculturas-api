<?php

namespace App\Http\Controllers;

use App\Jobs\PostPurchase;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

use Illuminate\Support\Facades\Cache;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController
{
    private function applyFilter($query, $filter)
    {
        if ($status = Arr::get($filter, 'status')) {
            if ($status == 1) {
                $query->where(fn($query) => $query->whereNull('shipped')->orWhere('shipped', false));
            } else if ($status == 2) {
                $query->where('shipped',  true);
            }
        }
    }

    public function index(Request $request)
    {
        $query = Purchase::query();
        $filter = $request->filter;
        $this->applyFilter($query, $filter);
        $purchases = $query->with('items.product')->paginate($request->get('per_page'));

        return $purchases;
    }

    public function show($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->load('items.product');
        return $purchase;
    }

    public function downloadPurchaseOrder($id)
    {
        $purchase = Purchase::findOrFail($id);
        $data = [
            'purchase' => $purchase,
        ];

        $pdf = Pdf::loadView('orders.purchase', $data);
        $pdf->setPaper('letter', 'landscape');
        $path = 'purchases/pedido_' . str_pad($purchase->id, 6, '0', STR_PAD_LEFT) . '_' . str_replace('.', '', microtime(true)) . '.pdf';
        $pdf->save($path, 'local');

        return response([
            'download_url' => Storage::temporaryUrl($path, now()->addMinutes(30))
        ]);
    }



    public function store(Request $request)
    {
        $payload = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required'],
            'items.*.quantity' => ['required'],
            'value' => ['required'],
            'payment_type' => ['required', 'in:transferencia,stripe'],
            'customer_name' => ['required'],
            'customer_mail' => ['required'],
            'customer_address' => ['required'],
            'customer_state' => ['required'],
            'customer_city' => ['required'],
            'customer_zip' => ['required'],
            'customer_phone' => ['required'],
            'payment_method' => ['required_if:payment_type,stripe']
        ]);

        $purchase = DB::transaction(function () use ($payload) {
            $totalAmount = 0;
            $shippingFee = 0;
            $items = [];
            foreach ($payload['items'] as $item) {
                logger("items", $item);
                $product = Product::find($item['product_id']);
                $quantity = (int) $item['quantity'];
                do {
                    if ($product->stock < $quantity) {
                        abort(response([
                            'message' => 'Stock insuficiente para el producto ' . $product->name . ". Solo quedan {$product->stock} disponibles.",
                            'code' => 'INSUFFICIENT_PRODUCT_STOCK'
                        ], 409));
                    }
                    $updated = $product->optimisticUpdate([
                        'stock' => DB::raw("`stock` - $quantity")
                    ]);
                    if ($updated) break;
                    $product->refresh();
                } while (true);

                $unitPrice = $product->price;
                $subTotal = $unitPrice * $quantity;
                $totalAmount += $subTotal;
                $items[] = [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                ];

                $shippingFee += $product->shipping_value;
            }

            $paymentType = $payload['payment_type'];

            $purchase = new Purchase();
            $purchase->date             = Carbon::now();
            $purchase->value            = $totalAmount + $shippingFee;
            $purchase->shipping_fee     = $shippingFee;
            $purchase->payment_type     = $payload['payment_type'];
            $purchase->customer_name    = $payload['customer_name'];
            $purchase->customer_mail    = $payload['customer_mail'];
            $purchase->customer_address = $payload['customer_address'];
            $purchase->customer_state   = $payload['customer_state'];
            $purchase->customer_city    = $payload['customer_city'];
            $purchase->customer_zip     = $payload['customer_zip'];
            $purchase->customer_phone   = $payload['customer_phone'];
            $purchase->promo_code       = $payload['promo_code'] ?? '';

            $purchase->save();
            $purchase->items()->createMany($items);

            if ($paymentType === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => $payload['value'] * 100,
                    'currency' => 'MXN',
                    'confirm' => true,
                    'return_url' => config('app.url') . '/checkout/thank-you',
                    'payment_method' => $payload['payment_method'],
                ]);

                if ($paymentIntent->status !== 'succeeded') {
                    abort(response([
                        'message' => 'El cargo no fue exitoso',
                        'error' => 'El cargo no se completó correctamente en Stripe.',
                    ], 500));
                }
            }

            PostPurchase::dispatch($purchase)->afterCommit();

            return $purchase;
        });


        return response()->json([
            'message' => 'Compra creada exitosamente, stock actualizado',
            'purchase' => $purchase,
            "is_ok" => true,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        abort_if($purchase->cancelled, response([
            'message' => 'No puedes modificar pedidos cancelados.'
        ], 409));

        $validated = $request->validate([
            'shipped' => 'nullable|boolean',
            'shipping_date' => 'nullable|date',
            'items' => 'sometimes|required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($purchase, $validated) {
            $purchase->fill(Arr::only($validated, ['shipped', 'shipping_date']));

            // Inserta los nuevos items si hay datos
            if (!empty($validated['items'])) {
                $purchase->shipping_fee = 0;
                $purchase->value = 0;
                $newItemsData = [];

                // Recupera y ordena los items actuales para luego revertir stock.
                $previousItems = $purchase->items
                    ->sortBy('product_id');

                // Revertir stock de los items previos.
                foreach ($previousItems as $item) {
                    if (!$item->product) {
                        continue;
                    }
                    $item->product->updateStock($item->quantity);
                }
                // Elimina los items anteriores.
                $purchase->items()->delete();

                // Ordena los nuevos items por product_id.
                $sortedNewItems = collect($validated['items'])->sortBy('product_id');

                foreach ($sortedNewItems as $itemData) {
                    $product = Product::find($itemData['product_id']);
                    if (!$product) {
                        abort(404, "Producto no encontrado.");
                    }
                    $quantity = $itemData['quantity'];

                    // Disminuye el stock del producto de forma segura.
                    $stockUpdated = $product->updateStock(-$quantity);
                    abort_if(!$stockUpdated, response([
                        'message' => 'Stock insuficiente para el producto ' . $product->name
                            . ". Solo quedan {$product->stock} disponibles.",
                        'code' => 'INSUFFICIENT_PRODUCT_STOCK'
                    ], 409));

                    // Determina el precio unitario:
                    // Si existía un item previo para el mismo producto, se utiliza su precio;
                    // de lo contrario, se calcula como precio actual.
                    $oldItem = $previousItems->firstWhere('product_id', $itemData['product_id']);
                    $unitPrice = $oldItem ? $oldItem->unit_price : $product->price;

                    $purchase->value += $unitPrice * $quantity;
                    $purchase->shipping_fee += $product->shipping_value;

                    $newItemsData[] = [
                        'product_id'  => $product->id,
                        'description' => $product->name,
                        'unit_price'  => $unitPrice,
                        'quantity'    => $quantity,
                    ];
                }

                $purchase->value += $purchase->shipping_fee;

                // Actualiza el pedido.
                $purchase->update();
                $purchase->items()->createMany($newItemsData);
            }
        });


        Cache::forget('purchase_' . $id);
        Cache::forget('purchases_all');

        return response()->json([
            'message' => 'Compra actualizada exitosamente',
            'purchase' => $purchase
        ], 200);
    }
}
