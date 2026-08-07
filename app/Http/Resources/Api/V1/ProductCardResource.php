<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    /**
     * تحويل المنتج إلى شكل "كارد" خفيف يستخدم في البحث، الصفحة الرئيسية، والتصنيفات
     */
    public function toArray(Request $request): array
    {
        return [




         'id'            => $this->id,
    'name'          => $this->name,
        'image_url'     => $this->base_image_url,

    // بيانات السعر
    'net_price'     => (float) ($this->netPrice ?? 0),
    'sale_price'    => $this->salePrice != null ? (float) $this->salePrice : 0.0,
    'final_price'   => (float) $this->final_price,
    'is_on_sale'    => (bool) $this->is_on_sale,

    // تواريخ العرض
    'sale_starts_at'=> $this->salePrice_start ?? "",
    'sale_ends_at'  => $this->salePrice_end ?? "",

    // بيانات إضافية
    'calories'      => $this->calories ?? "",
    'sku'           => $this->sku ?? "",

    'category_id'   => $this->category_id,
    'category_name' => $this->whenLoaded('category', fn($cat) => $cat->name),
            'has_variants'  => (bool) ($this->has_variants ?? false),


        ];
    }
}
