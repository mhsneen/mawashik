<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

class ProductPriceCalculator
{
    /**
     * @param  Product  $product  لازم يكون محمّل مسبقاً بـ: variants.attributeValues, attributeValues.attribute
     * @param  int|null  $variantId
     * @param  array  $modifierValueIds
     * @param  int  $quantity  الكمية المطلوبة (للفحص المخزون)
     * @return array{variant: \App\Models\ProductVariant|null, unit_price: float, modifier_lines: \Illuminate\Support\Collection}
     *
     * @throws InvalidArgumentException
     */
    public function calculate(
        Product $product,
        ?int $variantId,
        array $modifierValueIds = [],
        int $quantity = 1
    ): array {
        if (! $product->is_active) {
            throw new InvalidArgumentException(__('api.Product_Not_Available'));
        }

        if ($quantity < 1) {
            throw new InvalidArgumentException(__('api.Invalid_Quantity'));
        }

        $variant = $this->resolveVariant($product, $variantId, $quantity);

        // ✅ استخدام الدالة العادية لضمان تطبيق الخصم على سعر الفيرنت أو المنتج
 $unitPrice = $product->calculateFinalPrice($variant);

        $modifierLines = $this->resolveModifiers($product, $modifierValueIds, $quantity);

        foreach ($modifierLines as $line) {
            $unitPrice += $line['price'];
        }

        return [
            'variant' => $variant,
            'unit_price' => round($unitPrice, 2),
            'modifier_lines' => $modifierLines,
        ];
    }

    protected function resolveVariant(Product $product, ?int $variantId, int $quantity)
    {
        $hasVariants = $product->variants->isNotEmpty();

        if (! $variantId) {
            if ($hasVariants) {
                throw new InvalidArgumentException(__('api.Please_Select_Variant'));
            }

            if ($product->track_quantity && ! $product->hasStock($quantity)) {
                throw new InvalidArgumentException(__('api.Product_Out_Of_Stock'));
            }

            return null;
        }

        $variant = $product->variants->firstWhere('id', $variantId);

        if (! $variant || ! $variant->is_active) {
            throw new InvalidArgumentException(__('api.Invalid_Variant_Selected'));
        }

        // ✅ استخدام $quantity بدل $item['quantity'] ?? 1
        if ($variant->track_quantity && ! $variant->hasStock($quantity)) {
            throw new InvalidArgumentException(__('api.Variant_Out_Of_Stock'));
        }

        return $variant;
    }

    protected function resolveModifiers(Product $product, array $modifierValueIds, int $quantity)
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

            // ✅ استخدام $quantity بدل $item['quantity'] ?? 1
            if (! is_null($pivotQuantity) && (float) $pivotQuantity < (float) $quantity) {
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
