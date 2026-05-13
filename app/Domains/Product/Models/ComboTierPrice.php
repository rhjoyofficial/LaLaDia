<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ComboTierPrice extends Model
{
    protected $fillable = [
        'combo_id',
        'min_quantity',
        'discount_type',
        'discount_value',
        'has_free_delivery',
        'free_delivery_zones',
        'gift_product_variant_id',
        'gift_quantity',
    ];

    protected $casts = [
        'has_free_delivery'  => 'boolean',
        'free_delivery_zones' => 'array',
        'gift_quantity'      => 'integer',
        'discount_value'     => 'decimal:2',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function giftVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'gift_product_variant_id');
    }
}
