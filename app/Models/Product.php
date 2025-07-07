<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\OptimisticLock;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, OptimisticLock;

    protected $fillable = [
        'name',
        'presentation',
        'description',
        'price',
        'stock',
        'status',
        'shipping_value',
        'images',
    ];

    protected $casts  = [
        'images' => 'array',
        'price' => 'integer'
    ];

    /**
     * Actualiza el stock de un producto utilizando una actualización optimista.
     *
     * @param  int     $quantity
     * @param  int     $maxAttempts Número máximo de intentos (default: 10)
     * @return bool    Returns true is stock was updated, false otherwise
     *
     * @throws \Exception Si no se logra actualizar después del número máximo de intentos.
     */
    public function updateStock(int $quantity, int $maxAttempts = 10): bool
    {
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            if ($this->stock + $quantity < 0) {
                return false;
            }

            $updated = $this->optimisticUpdate([
                'stock' => DB::raw("`stock` + {$quantity}"),
            ]);

            if ($updated) {
                return true;
            }

            $this->refresh();
            $attempts++;
        }

        throw new \Exception("No se pudo actualizar el stock del producto {$this->name} después de $maxAttempts intentos", 500);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['images'] = array_map(fn($image) => asset($image), $array['images'] ?? []);
        return $array;
    }
}
