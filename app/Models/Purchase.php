<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'value',
        'shipping_fee',
        'customer_name',
        'customer_mail',
        'customer_address',
        'customer_country',
        'customer_state',
        'customer_city',
        'customer_zip',
        'customer_phone',
        'shipped',
        'shipping_date',
        'promo_code',
        'payment_type',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function casts(): array
    {
        return [
            'date' => 'datetime',
            'shipping_date' => 'datetime',
            'value' => 'integer',
            'shipping_fee' => 'integer',
            'shipped' => 'boolean'
        ];
    }
}
