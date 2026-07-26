<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * لازم يكون الـ Product محمّل مسبقاً بـ:
 * ->with(['variants.attributeValues.attribute', 'attributeValues.attribute'])
 * وإلا رح تصير N+1 queries.
 */
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'details' => $this->details,
            'image' => $this->image_url,
            'base_image' => $this->base_image_url,
            'calories' => $this->calories,
            'sku' => $this->sku,
            'net_price' => (float) $this->netPrice,
            'final_price' => (float) $this->final_price,
            'is_on_sale' => (bool) $this->is_on_sale,
            'has_variants' => $this->variants->isNotEmpty(),


                  // ← معلومات المخزون
        'track_quantity' => $this->track_quantity,
        'quantity' => $this->when($this->track_quantity, $this->quantity),
        'in_stock' => $this->inStock(),


            // خصائص تحدد الفيرنت (مقاس/لون/وزن..) — تُستخدم لبناء واجهة الاختيار
            'variation_groups' => $this->attributeValues
                ->filter(fn ($value) => (bool) $value->attribute->is_variation)
                ->groupBy('attribute_id')
                ->map(fn ($values, $attributeId) => [
                    'attribute_id' => (int) $attributeId,
                    'name' => $values->first()->attribute->name,
                    'type' => $values->first()->attribute->type,
                    'values' => $values->sortBy('pivot.sort_order')->values()->map(fn ($v) => [
                        'id' => $v->id,
                        'name' => $v->value,
                        'color_code' => $v->color_code,
                    ]),
                ])
                ->values(),

            // كل التركيبات (Variants) المتاحة فعلياً مع سعرها ومخزونها
            'variants' => $this->variants->where('is_active', true)->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'value_ids' => $variant->attributeValues->pluck('id'),
                'label' => $variant->attributeValues->pluck('value')->implode(' / '),
                'price' => (float) $variant->effective_price,
                'in_stock' => $variant->hasStock(),
            ])->values(),

            // مجموعات الإضافات (Modifiers) — بطاطا/صوص/حجم كوب..
            'modifier_groups' => $this->attributeValues
                ->filter(fn ($value) => ! $value->attribute->is_variation)
                ->groupBy('attribute_id')
                ->map(fn ($values, $attributeId) => [
                    'attribute_id' => (int) $attributeId,
                    'name' => $values->first()->attribute->name,
                    'type' => $values->first()->attribute->type,
                    'values' => $values->sortBy('pivot.sort_order')->values()->map(fn ($v) => [
                        'id' => $v->id,
                        'name' => $v->value,
                        'color_code' => $v->color_code,
                        'price' => (float) $v->pivot->price,
                        'in_stock' => is_null($v->pivot->quantity) || $v->pivot->quantity > 0,
                        'is_default' => (bool) $v->pivot->is_default,
                    ]),
                ])
                ->values(),
        ];
    }
}
