<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTierPrice extends Model
{
    protected $fillable = [
        'variant_id',
        'min_quantity',
        'discount_type',
        'discount_value',
        'has_free_delivery',
        'free_delivery_zones',
        'gift_product_variant_id',
        'gift_quantity'
    ];

    protected $casts = [
        'has_free_delivery' => 'boolean',
        'free_delivery_zones' => 'array',
        'gift_quantity' => 'integer',
        'discount_value' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function giftVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'gift_product_variant_id');
    }
}
