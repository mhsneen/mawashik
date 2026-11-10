<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        // نستخرج البيانات التي مررناها من الكنترولر
        $product = $this->resource['product'];
        $calc = $this->resource['calc'];
        $quantity = $this->resource['quantity'];
        $itemTotal = $this->resource['itemTotal'];

        return [
            'product_id'    => $product->id,
            'product_name'  => $product->name,
            'image_url'     => $product->base_image_url, // <--- هنا أضفنا صورة المنتج

            'variant_id'    => $calc['variant']?->id,
            'variant_label' => $calc['variant']
                ? $calc['variant']->attributeValues->pluck('value')->implode(' / ')
                : null,

            'modifiers'     => collect($calc['modifier_lines'])->map(fn($line) => [
                'attribute_value_id' => $line['attribute_value_id'],
                'name'               => $line['value_name'],
                'price'              => $line['price'],
            ]),

            'quantity'      => $quantity,
            'unit_price'    => $calc['unit_price'],
            'total_price'   => $itemTotal,
        ];
    }
}
