<?php

namespace App\Services\Payments\DTO;

class GatewayInitResult
{
    public function __construct(
        public bool $success,
        public ?string $redirectUrl = null,   // رابط صفحة الدفع (WebView) - يفتحه التطبيق مباشرة
        public ?string $reference = null,     // معرّف العملية عند البوابة (session id / checkoutId / invoiceId ...)
        public ?string $message = null,
        public array $extra = [],             // أي بيانات إضافية يحتاجها التطبيق (مثال: checkoutId لعرض HyperPay widget)
        public array $raw = [],
    ) {
    }

    public static function ok(?string $redirectUrl, ?string $reference, array $extra = [], array $raw = []): self
    {
        return new self(true, $redirectUrl, $reference, null, $extra, $raw);
    }

    public static function fail(string $message, array $raw = []): self
    {
        return new self(false, null, null, $message, [], $raw);
    }

    public function toArray(): array
    {
        return [
            'success'      => $this->success,
            'redirect_url' => $this->redirectUrl,
            'reference'    => $this->reference,
            'message'      => $this->message,
            'extra'        => $this->extra,
        ];
    }
}
