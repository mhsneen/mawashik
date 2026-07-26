<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

/**
 * الحساب دائماً من بيانات السيرفر (المنتج/الفيرنت/الإضافات) وليس مما يرسله العميل،
 * لمنع أي محاولة تلاعب بالسعر من الفرونت إند.
 *
 * الاستخدام:
 *   $result = $calculator->calculate($product, $variantId, $modifierValueIds);
 *   $result['unit_price']      => سعر الوحدة النهائي (فيرنت أو أساسي + الإضافات)
 *   $result['variant']         => ProductVariant|null المختار
 *   $result['modifier_lines']  => تفاصيل كل إضافة (للحفظ Snapshot أو للعرض)
 */
class ProductPriceCalculator
{
    /**
     * @param  Product  $product  لازم يكون محمّل مسبقاً بـ: variants.attributeValues, attributeValues.attribute
     * @param  int|null  $variantId
     * @param  array  $modifierValueIds
     * @return array{variant: \App\Models\ProductVariant|null, unit_price: float, modifier_lines: \Illuminate\Support\Collection}
     *
     * @throws InvalidArgumentException  لو الاختيار غير صالح أو غير متوفر بالمخزون
     */
    public function calculate(Product $product, ?int $variantId, array $modifierValueIds = []): array
    {
        if (! $product->is_active) {
            throw new InvalidArgumentException(__('api.Product_Not_Available'));
        }

        $variant = $this->resolveVariant($product, $variantId);

        $unitPrice = (float) ($variant?->price ?? $product->final_price);

        $modifierLines = $this->resolveModifiers($product, $modifierValueIds);

        foreach ($modifierLines as $line) {
            $unitPrice += $line['price'];
        }

        return [
            'variant' => $variant,
            'unit_price' => round($unitPrice, 2),
            'modifier_lines' => $modifierLines,
        ];
    }

    /**
     * يتحقق من اختيار الفيرنت (لو المنتج أصلاً عنده فيرنتس، يصير اختياره إجباري)
     * ومن توفره بالمخزون.
     */
   protected function resolveVariant(Product $product, ?int $variantId)
{
    $hasVariants = $product->variants->isNotEmpty();

    if (! $variantId) {
        if ($hasVariants) {
            throw new InvalidArgumentException(__('api.Please_Select_Variant'));
        }

        // ← تحقق من المخزون فقط لو يتبع المخزون
        if ($product->track_quantity && $product->outOfStock()) {
            throw new InvalidArgumentException(__('api.Product_Out_Of_Stock'));
        }

        return null;
    }

    $variant = $product->variants->firstWhere('id', $variantId);

    if (! $variant || ! $variant->is_active) {
        throw new InvalidArgumentException(__('api.Invalid_Variant_Selected'));
    }

    // ← تحقق من المخزون فقط لو يتبع المخزون
    if ($variant->track_quantity && ! $variant->hasStock($item['quantity'] ?? 1)) {
        throw new InvalidArgumentException(__('api.Variant_Out_Of_Stock'));
    }

    return $variant;
}

    /**
     * يتحقق إن كل قيمة إضافة مطلوبة تابعة فعلاً لهذا المنتج ونوعها modifier
     * (مو variation)، ومتوفرة بالمخزون لو كان لها مخزون محدد.
     */
    protected function resolveModifiers(Product $product, array $modifierValueIds)
    {
        $modifierValueIds = array_values(array_unique(array_map('intval', $modifierValueIds)));

        if (empty($modifierValueIds)) {
            return collect();
        }

        $availableModifiers = $product->attributeValues
            ->filter(fn ($value) => ! $value->attribute->is_variation)
            ->keyBy('id');

        $lines = collect();

        foreach ($modifierValueIds as $valueId) {
            $value = $availableModifiers->get($valueId);

            if (! $value) {
                throw new InvalidArgumentException(__('api.Invalid_Modifier_Selected'));
            }

            $pivotQuantity = $value->pivot->quantity;

          if (! is_null($pivotQuantity) && (float) $pivotQuantity < (float) ($item['quantity'] ?? 1)) {
    throw new InvalidArgumentException(
        __('api.Modifier_Out_Of_Stock', ['name' => $value->value])
    );
}

            $lines->push([
                'attribute_id' => $value->attribute_id,
                'attribute_value_id' => $value->id,
                'attribute_name' => $value->attribute->name,
                'value_name' => $value->value,
                'color_code' => $value->color_code,
                'price' => (float) $value->pivot->price,
            ]);
        }

        return $lines;
    }
}
