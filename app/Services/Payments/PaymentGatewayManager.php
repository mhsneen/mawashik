<?php

namespace App\Services\Payments;

use App\Models\PaymentMethod;
use App\Services\Payments\Contracts\PaymentGatewayContract;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /**
     * إرجاع درايفر البوابة الجاهز للاستخدام حسب الكود.
     * يرمي استثناء إذا كانت البوابة غير موجودة أو غير مُفعّلة من لوحة التحكم.
     */
    public function driver(string $code): PaymentGatewayContract
    {
        $method = PaymentMethod::where('code', $code)->first();

        if (!$method || !$method->is_enabled) {
            throw new InvalidArgumentException("Payment gateway [{$code}] is not enabled.");
        }

        $catalog = config('payment_gateways.' . $code);

        if (!$catalog || !isset($catalog['driver']) || !class_exists($catalog['driver'])) {
            throw new InvalidArgumentException("Payment gateway [{$code}] is not registered in config/payment_gateways.php.");
        }

        return new $catalog['driver']($method);
    }

    /** كل البوابات المُفعّلة حالياً (تُستخدم لعرضها في التطبيق) */
    public function enabledMethods()
    {
        return PaymentMethod::enabled()->orderBy('sort_order')->get();
    }

    public function isEnabled(string $code): bool
    {
        return PaymentMethod::where('code', $code)->where('is_enabled', true)->exists();
    }
}
