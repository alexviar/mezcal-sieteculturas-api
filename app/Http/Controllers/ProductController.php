<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class ProductController
{


    public function index(Request $request)
    {
        $products = Product::paginate($request->get('per_page'));

        return $products;
    }

    public function show($id)
    {
        try {
            $cacheKey = 'product_' . $id;
            $cacheDuration = 3600;

            $product = Cache::remember($cacheKey, $cacheDuration, function () use ($id) {
                return Product::findOrFail($id);
            });

            return response()->json(['message' => 'Producto obtenido exitosamente', 'product' => $product], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al recuperar producto',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required'],
            'presentation' => ['required'],
            'description' => ['required'],
            'price' => ['required'],
            'shipping_value' => ['required'],
            'stock' => ['required'],
            'status' => ['required'],
            'images' => ['required', 'array'],
            'images.*' => ['image'],
        ]);


        $payload['images'] = array_map(fn($image) => Storage::url($image->store('products', 'public')), $payload['images']);
        $product = Product::create($payload);

        return $product;
    }




    public function update(Request $request, $id)
    {
        $payload = $request->validate([
            'name' => ['required'],
            'presentation' => ['required'],
            'description' => ['required'],
            'price' => ['required'],
            'shipping_value' => ['required'],
            'stock' => ['required'],
            'status' => ['required'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['sometimes', 'image'],
        ]);

        $product = Product::findOrFail($id);
        
        if (isset($payload['images'])) {
            foreach($product->images as $image){
                Storage::disk('public')->delete($image);
            }
            $payload['images'] = array_map(fn($image) => Storage::url($image->store('products', 'public')), $payload['images']);
        }
        
        $product->update($payload);

        return $product;
    }


    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            Cache::forget('product_' . $id);
            Cache::forget('products_all');

            return response()->json(['message' => 'Producto eliminado exitosamente'], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al eliminar producto',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }
}
