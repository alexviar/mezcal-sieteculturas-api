<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseItemFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'description',
        'unit_price',
        'quantity'
    ];

    public function subtotal(): Attribute
    {
        return Attribute::get(fn() => $this->unit_price * $this->quantity);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
