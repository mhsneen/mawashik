<?php

namespace App\Http\Resources\Api\V1\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemAttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'attribute_id'       => $this->attribute_id,
            'attribute_value_id' => $this->attribute_value_id,
            'attribute_name'     => $this->attribute_name ?? '',
            'value_name'         => $this->value_name ?? '',
            'color_code'         => $this->color_code ?? '',
            'price'              => (float) $this->price,
        ];
    }
}
