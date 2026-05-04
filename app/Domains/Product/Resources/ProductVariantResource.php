<?php

namespace App\Domains\Product\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'sku'              => $this->sku,
            'price'            => (float) $this->price,
            'final_price'      => (float) $this->final_price,
            'discount_percent' => $this->discount_percent,
            'discount_type'    => $this->discount_type,
            'discount_value'   => $this->discount_value,
            'sale_ends_at'     => $this->sale_ends_at,
            'stock'            => (int) $this->stock,
            'reserved_stock'   => (int) $this->reserved_stock,
            'available_stock'  => $this->available_stock,
            'weight_grams'     => $this->weight_grams,
            'is_active'        => (bool) $this->is_active,
            // Public-facing minimal tier data (used by store pages)
            'tiers'            => ProductTierResource::collection($this->whenLoaded('tierPrices')),
            // Full admin tier data (used by admin product edit UI)
            'tier_prices'      => $this->relationLoaded('tierPrices')
                ? $this->tierPrices->map(fn($t) => [
                    'id'                      => $t->id,
                    'min_quantity'            => $t->min_quantity,
                    'discount_type'           => $t->discount_type,
                    'discount_value'          => (float) $t->discount_value,
                    'has_free_delivery'       => (bool) $t->has_free_delivery,
                    'free_delivery_zones'     => $t->free_delivery_zones ?? [],
                    'gift_product_variant_id' => $t->gift_product_variant_id,
                    'gift_variant_name'       => $t->relationLoaded('giftVariant')
                        ? ($t->giftVariant?->product?->name . ' — ' . $t->giftVariant?->title)
                        : null,
                    'gift_quantity'           => $t->gift_quantity ?? 1,
                ])->values()
                : [],
        ];
    }
}
