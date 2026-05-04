<?php

namespace App\Domains\Product\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductTierResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'qty'            => $this->min_quantity,
      'type'           => $this->discount_type,
      'value'          => $this->discount_value,
      'free_delivery'  => (bool) $this->has_free_delivery,
      'delivery_zones' => $this->free_delivery_zones ?? [],
      'gift_variant_id'=> $this->gift_product_variant_id,
      'gift_qty'       => $this->gift_quantity,
    ];
  }
}
