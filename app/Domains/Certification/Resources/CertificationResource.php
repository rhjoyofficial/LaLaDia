<?php

namespace App\Domains\Certification\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'category'           => $this->category,
            'organization'       => $this->organization,
            'given_date'         => $this->given_date ? $this->given_date->format('Y-m-d') : null,
            'expiry_date'        => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'additional_details' => $this->additional_details,
            'logo_path'          => $this->logo_path,
            'logo_url'           => $this->logo_url,
            'image_path'         => $this->image_path,
            'image_url'          => $this->image_url,
            'is_active'          => $this->is_active,
            'sort_order'         => $this->sort_order,
            'created_at'         => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
