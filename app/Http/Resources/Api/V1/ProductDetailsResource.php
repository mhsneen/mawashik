<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. تجميع خصائص الفاريانتس (المقاس، اللون...)
        $variationGroups = $this->whenLoaded('attributeValues', function () {
            return $this->variationValues()
                ->groupBy('attribute_id')
                ->map(function ($values, $attrId) {
                    $attribute = $values->first()->attribute;
                    $type = $values->contains(fn($v) => !is_null($v->color_code)) ? 'color' : 'select';

                    return [
                        'attribute_id' => (int) $attrId,
                        'name'         => $attribute->name ?? "",
                        'type'         => $type,
                        'values'       => $values->map(fn($v) => [
                            'id'         => $v->id,
                            'name'       => $v->value ?? "",
                            'color_code' => $v->color_code ?? "",
                        ])->values(),
                    ];
                })->values();
        });

        // 2. تجميع الإضافات/الموديفيرز (التغليف، الحجم الإضافي...)
        $modifierGroups = $this->whenLoaded('attributeValues', function () {
            return $this->modifierValues()
                ->groupBy('attribute_id')
                ->map(function ($values, $attrId) {
                    $attribute = $values->first()->attribute;
                    $type = $values->contains(fn($v) => !is_null($v->color_code)) ? 'color' : 'select';

                    return [
                        'attribute_id' => (int) $attrId,
                        'name'         => $attribute->name ?? "",
                        'type'         => $type,
                        'values'       => $values->map(fn($v) => [
                            'id'         => $v->id,
                            'name'       => $v->value ?? "",
                            'color_code' => $v->color_code ?? "",
                            'price'      => (float) ($v->pivot->price ?? 0),
                            'in_stock'   => ($v->pivot->quantity === null || $v->pivot->quantity > 0),
                            'is_default' => (bool) ($v->pivot->is_default ?? false),
                        ])->values(),
                    ];
                })->values();
        });

        return [
            // بيانات أساسية (تُرجع "" لو فاضية)
            'id'              => $this->id,
            'name'            => $this->name ?? "",
            'slug'            => $this->slug ?? "",
            'description'     => $this->description ?? "",
            'details'         => $this->details ?? "",

            // الصور
            'image_url'       => $this->image_url ?? "",
            'base_image_url'  => $this->base_image_url ?? "",
            'images'          => ProductImageResource::collection($this->whenLoaded('productimages')),

            // بيانات إضافية (تُرجع "" لو فاضية)
            'calories'        => $this->calories ?? "",
            'sku'             => $this->sku ?? "",

            // بيانات السعر (تُرجع 0.0 لو فاضية)
            'net_price'       => (float) ($this->netPrice ?? 0),
            'sale_price'      => $this->salePrice != null ? (float) $this->salePrice : 0.0,
            'final_price'     => (float) ($this->final_price ?? 0),
            'is_on_sale'      => (bool) $this->is_on_sale,
            'sale_starts_at'  => $this->salePrice_start ?? "",
            'sale_ends_at'    => $this->salePrice_end ?? "",

            // المخزون
            'has_variants'    => $this->whenLoaded('attributeValues', fn() => $this->variationValues()->isNotEmpty(), false),            'track_quantity'  => (bool) $this->track_quantity,
            'quantity'        => $this->quantity ?? "0.00",
            'in_stock'        => $this->inStock(),

 // الوحدة (إذا كان المنتج مرتبط بوحدة)
    'unit'               => $this->whenLoaded('unit', fn($unit) => [
        'id'   => $unit->id,
        'name' => $unit->name ?? "", // name يرجع تلقائياً مترجم بسبب astrotomic/translatable
    ]),

            // الفاريانتس والموديفيرز
            'variation_groups'=> $variationGroups ?? [],
            'variants'        => VariantResource::collection($this->whenLoaded('variants')),
            'modifier_groups' => $modifierGroups ?? [],

            // القسم والبراند
            'category'        => $this->whenLoaded('category', fn($cat) => [
                'id'        => $cat->id,
                'name'      => $cat->name ?? "",
                'image_url' => $cat->image_url ?? "",
            ]),
            'brand'           => $this->whenLoaded('brand', fn($b) => [
                'id'        => $b->id,
                'name'      => $b->name ?? "",
                'image_url' => $b->image_url ?? "",
            ]),

            // === مواشي وسيط (البند الثالث) ===

            // الفيديو (إذا وُجد)
            'video_url' => $this->video_url ?? "",

            // المواصفات (JSON: [{key, value}, ...])
            'specifications' => $this->specifications ?? [],

            // خدمة الذبح: قيمة واحدة (مش max/min)
            'slaughter' => [
                'available'   => (bool) ($this->slaughter_available ?? false),
                'required'    => (bool) ($this->slaughter_required  ?? false),
                'price'       => $this->slaughter_price !== null
                                    ? (float) $this->slaughter_price
                                    : null,  // null = يستخدم السعر الافتراضي من الإعدادات
                'effective_price' => (float) $this->slaughter_price_effective,
            ],

            // حالة اعتماد المنتج
            'approval' => [
                'status'           => $this->product_status ?? 'pending',
                'approved_at'      => $this->approved_at?->toIso8601String(),
                'rejected_at'      => $this->rejected_at?->toIso8601String(),
                'rejection_reason' => $this->rejection_reason ?? "",
            ],

            // التاجر + الموقع الجغرافي (يُورَّث من التاجر)
            'trader' => $this->whenLoaded('trader', fn($t) => [
                'id'              => $t->id,
                'name'            => $t->name ?? "",
                'store_address'   => $t->store_address ?? "",
                'store_lat'       => $t->store_lat,
                'store_lng'       => $t->store_lng,
                'city_id'         => $t->city_id,
                'area_id'         => $t->area_id,
                'neighborhood_id' => $t->neighborhood_id,
                'is_approved'     => $t->isApprovedTrader(),
            ]),
        ];
    }
}
