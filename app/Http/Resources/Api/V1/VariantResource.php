<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'sku'       => $this->sku ?? "",
            'value_ids' => $this->whenLoaded('attributeValues', fn() => $this->attributeValues->pluck('id')->toArray()),
            'label'     => $this->whenLoaded('attributeValues', fn() => $this->attributeValues->pluck('value')->join(' / ')),
            'price'     => (float) ($this->price ?? 0),

            // التحقق من المخزون بناءً على حقول ProductVariant (ليس Product)
            'in_stock'  => ($this->is_active ?? true) && ($this->quantity === null || $this->quantity > 0),
        ];
    }
}
