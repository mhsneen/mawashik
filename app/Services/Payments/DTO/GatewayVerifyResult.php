<?php

namespace App\Services\Payments\DTO;

class GatewayVerifyResult
{
    public function __construct(
        public bool $success,   // نجح الاستعلام نفسه (لا يعني بالضرورة أن الدفع ناجح)
        public bool $paid,      // هل الدفع فعلاً مكتمل/محصّل
        public ?string $reference = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
        public array $raw = [],
    ) {
    }

    public static function paid(string $reference, ?float $amount = null, ?string $currency = null, array $raw = []): self
    {
        return new self(true, true, $reference, $amount, $currency, null, $raw);
    }

    public static function notPaid(string $reference = null, string $message = null, array $raw = []): self
    {
        return new self(true, false, $reference, null, null, $message, $raw);
    }

    public static function fail(string $message, array $raw = []): self
    {
        return new self(false, false, null, null, null, $message, $raw);
    }
}
