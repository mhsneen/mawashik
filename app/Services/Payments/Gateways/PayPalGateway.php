<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalGateway extends AbstractPaymentGateway
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    protected function accessToken(): ?string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->config('client_id'), $this->config('client_secret'))
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        return $response->successful() ? $response->json('access_token') : null;
    }

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $token = $this->accessToken();
        if (!$token) {
            return GatewayInitResult::fail('Unable to authenticate with PayPal.');
        }

        $response = Http::withToken($token)
            ->post($this->baseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => (string) $order->id,
                    'amount' => [
                        'currency_code' => strtoupper($order->currency ?? 'USD'),
                        'value'         => number_format($order->final_price, 2, '.', ''),
                    ],
                ]],
                // application_context مُعلَّم Deprecated من PayPal؛ البديل الحالي الموصى به هو
                // payment_source.paypal.experience_context (نفس الوظيفة، دعم أطول أمداً).
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'return_url' => $this->returnUrl($order->id) . '&status=success',
                            'cancel_url' => $this->returnUrl($order->id) . '&status=cancel',
                            'user_action' => 'PAY_NOW',
                        ],
                    ],
                ],
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['id'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail($data['message'] ?? 'PayPal error', $data ?? []);
        }

        // عند تمرير payment_source.paypal صراحة، بايبال يغيّر اسم رابط التوجيه من rel=approve
        // إلى rel=payer-action - نبحث عن أي منهما لضمان التوافق.
        $links = collect($data['links'] ?? []);
        $approveUrl = $links->firstWhere('rel', 'payer-action')['href']
            ?? $links->firstWhere('rel', 'approve')['href']
            ?? null;

        return GatewayInitResult::ok($approveUrl, $data['id'], [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $token = $this->accessToken();
        if (!$token) {
            return GatewayVerifyResult::fail('Unable to authenticate with PayPal.');
        }

        // التقاط الدفعة فعلياً بعد موافقة المستخدم (Capture)
        $response = Http::withToken($token)
            ->post($this->baseUrl() . "/v2/checkout/orders/{$reference}/capture");

        $data = $response->json();

        $status = $data['status'] ?? null;

        if ($response->successful() && $status === 'COMPLETED') {
            $amount   = data_get($data, 'purchase_units.0.payments.captures.0.amount.value');
            $currency = data_get($data, 'purchase_units.0.payments.captures.0.amount.currency_code');
            return GatewayVerifyResult::paid($reference, $amount ? (float) $amount : null, $currency, $data);
        }

        return GatewayVerifyResult::notPaid($reference, $status, $data ?? []);
    }

       public function handleWebhook(Request $request): GatewayVerifyResult
    {
        $token = $this->accessToken();
        $webhookId = $this->config('webhook_id');

        // ✅ رفض الطلب إذا لم يتم إعداد الـ Webhook ID
        if (!$token || !$webhookId) {
            return GatewayVerifyResult::fail('PayPal webhook not configured properly.');
        }

        // ✅ التحقق من التوقيع عبر سيرفرات بايبال
        $verifyResponse = Http::withToken($token)->post($this->baseUrl() . '/v1/notifications/verify-webhook-signature', [
            'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
            'cert_url'          => $request->header('PAYPAL-CERT-URL'),
            'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
            'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            'webhook_id'        => $webhookId,
            'webhook_event'     => $request->all(),
        ]);

        $verificationStatus = $verifyResponse->json('verification_status');

        // ✅ إذا لم يكن التوقيع صحيحاً، ارفض الطلب فوراً
        if (!$verifyResponse->successful() || $verificationStatus !== 'SUCCESS') {
            return GatewayVerifyResult::fail('Invalid PayPal webhook signature.');
        }

        $payload = $request->json()->all();
        $eventType = $payload['event_type'] ?? null;
        $resource  = $payload['resource'] ?? [];

        if (in_array($eventType, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED'])) {
            $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? ($resource['id'] ?? null);
            $amount  = data_get($resource, 'amount.value');
            $currency = data_get($resource, 'amount.currency_code');
            return GatewayVerifyResult::paid((string) $orderId, $amount ? (float) $amount : null, $currency, $payload);
        }

        return GatewayVerifyResult::notPaid(null, $eventType, $payload);
    }
}
