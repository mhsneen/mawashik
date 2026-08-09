<?php

namespace App\Http\Resources\Api\V1\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'product_id'     => $this->product_id,
            'product_name'   => $this->product?->name ?? '',
            'product_image'  => $this->product?->base_image_url ?? '',
            'category_name'  => $this->product?->category?->name ?? '',

            'variant_id'     => $this->product_variant_id,
            'variant_label'  => $this->variant_label_snapshot ?? '',

            'quantity'       => (float) $this->quantity,
            'unit_name'      => $this->product?->unit?->name ?? '',

            'unit_price'     => (float) $this->unit_price,
            'total_price'    => (float) $this->total_price,

            'attributes'     => OrderItemAttributeResource::collection($this->whenLoaded('attributes')),
        ];
    }
}
