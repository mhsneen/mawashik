<?php

namespace App\Services\Payments;

use App\Models\PaymentMethod;
use App\Services\Payments\Contracts\PaymentGatewayContract;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayContract
{
    protected PaymentMethod $method;

    public function __construct(PaymentMethod $method)
    {
        $this->method = $method;
    }

    public function code(): string
    {
        return $this->method->code;
    }

    protected function isTestMode(): bool
    {
        return $this->method->mode === 'test';
    }

    /** يقرأ حقل من إعدادات هذه البوابة كما أدخلها الأدمن في لوحة التحكم */
    protected function config(string $key, $default = null)
    {
        return $this->method->getConfigValue($key, $default);
    }

    /** رابط الرجوع بعد الدفع (يفتحه المتصفح/الـWebView داخل التطبيق) */
    protected function returnUrl(int $orderId): string
    {
        return route('api.payments.return', [
            'gateway'  => $this->code(),
            'order_id' => $orderId,
        ]);
    }

    /** رابط الـ Webhook العام لهذه البوابة (تُسجَّل عند مزوّد الدفع) */
    protected function webhookUrl(): string
    {
        return route('api.payments.webhook', ['gateway' => $this->code()]);
    }

    protected function logInfo(string $message, array $context = []): void
    {
        Log::channel(config('logging.default'))->info("[payment:{$this->code()}] {$message}", $context);
    }

    protected function logError(string $message, array $context = []): void
    {
        Log::channel(config('logging.default'))->error("[payment:{$this->code()}] {$message}", $context);
    }

    /** تحويل المبلغ لأصغر وحدة عملة (هللة/سنت) - تحتاجها HyperPay/Moyasar وغيرها */
    protected function toMinorUnits(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
