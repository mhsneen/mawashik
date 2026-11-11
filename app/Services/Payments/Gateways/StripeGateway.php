<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StripeGateway extends AbstractPaymentGateway
{
    protected string $baseUrl = 'https://api.stripe.com/v1';

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $response = Http::asForm()
            ->withToken($this->config('secret_key'))
            ->post($this->baseUrl . '/checkout/sessions', [
                'mode'                          => 'payment',
                'client_reference_id'           => $order->id,
                'success_url'                   => $this->returnUrl($order->id) . '&status=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'                    => $this->returnUrl($order->id) . '&status=cancel',
                'line_items[0][price_data][currency]'                => strtolower($order->currency ?? 'sar'),
                'line_items[0][price_data][product_data][name]'      => 'Order #' . $order->id,
                'line_items[0][price_data][unit_amount]'             => $this->toMinorUnits($order->online_amount),
                'line_items[0][quantity]'                            => 1,
                'metadata[order_id]'           => $order->id,
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['id'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail($data['error']['message'] ?? 'Stripe error', $data ?? []);
        }

        return GatewayInitResult::ok($data['url'] ?? null, $data['id'], [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withToken($this->config('secret_key'))
            ->get($this->baseUrl . '/checkout/sessions/' . $reference);

        $data = $response->json();

        if (!$response->successful()) {
            return GatewayVerifyResult::fail($data['error']['message'] ?? 'Stripe verify error', $data ?? []);
        }

        if (($data['payment_status'] ?? null) === 'paid') {
            return GatewayVerifyResult::paid(
                $reference,
                ($data['amount_total'] ?? 0) / 100,
                strtoupper($data['currency'] ?? 'SAR'),
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, $data['payment_status'] ?? null, $data);
    }

        public function handleWebhook(Request $request): GatewayVerifyResult
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = $this->config('webhook_secret');

        // ✅ التحقق الإلزامي: لو السر أو التوقيع غير موجود، ارفض الطلب (Fail-Closed)
        if (!$secret || !$sigHeader) {
            return GatewayVerifyResult::fail('Stripe webhook secret or signature missing.');
        }

        // ✅ التحقق من تطابق التوقيع
        if (!$this->verifySignature($payload, $sigHeader, $secret)) {
            return GatewayVerifyResult::fail('Invalid Stripe webhook signature.');
        }

        $event = json_decode($payload, true);
        $type  = $event['type'] ?? null;
        $obj   = $event['data']['object'] ?? [];

        if ($type === 'checkout.session.completed' && ($obj['payment_status'] ?? null) === 'paid') {
            return GatewayVerifyResult::paid(
                $obj['id'] ?? '',
                ($obj['amount_total'] ?? 0) / 100,
                strtoupper($obj['currency'] ?? 'SAR'),
                $event
            );
        }

        return GatewayVerifyResult::notPaid($obj['id'] ?? null, $type, $event ?? []);
    }

    /** توقيع Stripe: HMAC-SHA256 لـ "{timestamp}.{payload}" باستخدام webhook secret */
    protected function verifySignature(string $payload, string $sigHeader, string $secret): bool
    {
        $parts = [];
        foreach (explode(',', $sigHeader) as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, null);
            $parts[$k] = $v;
        }

        $timestamp = $parts['t'] ?? null;
        $signature = $parts['v1'] ?? null;

        if (!$timestamp || !$signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
