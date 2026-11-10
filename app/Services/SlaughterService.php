<?php

namespace App\Services;

use App\Models\{OrderItem, Product, Setting};
use Illuminate\Support\Collection;

/**
 * SlaughterService — خدمة الذبح الاختيارية.
 *
 * المسؤوليات:
 *  - التحقق من تفعيل الخدمة عامة.
 *  - حساب سعر الذبح الفعلي لمنتج (سعر محدد على المنتج أو الافتراضي من الإعدادات).
 *  - حساب إجمالي الذبح لطلب.
 *  - التحقق من قيود الحد الأدنى والأقصى للسعر (لمحاسبة الأدمن).
 *
 * قاعدة التحقق:
 *  - لو المنتج مفعّل عليه slaughter_available + العميل اختار slaughter_selected، يُحسب سعر الذبح.
 *  - لو slaughter_required=true على المنتج، الذبح إلزامي ولا يمكن للعميل رفضه.
 */
class SlaughterService
{
    public function isEnabled(): bool
    {
        return Setting::where('key', 'slaughter_service_active')->value('plain_value') == '1';
    }

    public function defaultPrice(): float
    {
        return (float) (Setting::where('key', 'slaughter_default_price')->value('plain_value') ?? 0);
    }

    public function minPrice(): float
    {
        return (float) (Setting::where('key', 'slaughter_min_price')->value('plain_value') ?? 0);
    }

    public function maxPrice(): float
    {
        return (float) (Setting::where('key', 'slaughter_max_price')->value('plain_value') ?? 0);
    }

    /**
     * سعر الذبح الفعلي لمنتج معين.
     */
    public function priceForProduct(Product $product): float
    {
        if (!$this->isEnabled() || !$product->slaughter_available) {
            return 0;
        }
        return $product->getSlaughterPriceEffectiveAttribute();
    }

    /**
     * هل الذبح إلزامي لهذا المنتج؟
     */
    public function isRequiredForProduct(Product $product): bool
    {
        return $this->isEnabled()
            && $product->slaughter_available
            && (bool) $product->slaughter_required;
    }

    /**
     * حساب إجمالي الذبح لعناصر طلب.
     *
     * @param Collection<OrderItem> $items مع relation product محمّلة
     * @return float إجمالي الذبح
     */
    public function totalForOrder(Collection $items): float
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $total = 0;
        foreach ($items as $item) {
            if ($item->slaughter_selected) {
                $total += (float) $item->slaughter_total;
            }
        }
        return round($total, 2);
    }

    /**
     * التحقق من سعر ذبح محدد يدوياً على منتج قبل الحفظ (في لوحة التحكم).
     */
    public function validatePrice(?float $price): array
    {
        if (is_null($price)) {
            return ['valid' => true, 'message' => null];
        }

        if ($price < 0) {
            return ['valid' => false, 'message' => __('dashboard.slaughter_price_must_be_positive')];
        }

        if ($this->minPrice() > 0 && $price < $this->minPrice()) {
            return ['valid' => false, 'message' => __('dashboard.slaughter_price_below_min')];
        }

        if ($this->maxPrice() > 0 && $price > $this->maxPrice()) {
            return ['valid' => false, 'message' => __('dashboard.slaughter_price_above_max')];
        }

        return ['valid' => true, 'message' => null];
    }
}
