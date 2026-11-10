<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TamaraGateway extends AbstractPaymentGateway
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://api-sandbox.tamara.co'
            : 'https://api.tamara.co';
    }

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $user = $order->user;

        $response = Http::withToken($this->config('api_token'))
            ->acceptJson()
            ->post($this->baseUrl() . '/checkout', [
                'order_reference_id' => (string) $order->id,
                'order_number'       => (string) $order->id,
                'total_amount' => [
                    'amount'   => number_format($order->final_price, 2, '.', ''),
                    'currency' => $order->currency ?? 'SAR',
                ],
                'description'  => 'Order #' . $order->id,
                'country_code' => 'SA',
                'payment_type' => 'PAY_BY_LATER',
                'consumer' => [
                    'first_name'   => $user->name ?? 'Customer',
                    'last_name'    => '-',
                    'phone_number' => $user->mobile ?? '',
                    'email'        => $user->email ?? 'guest@example.com',
                ],
                'items' => $order->items->map(function ($item) use ($order) {
                    return [
                        'name'        => $item->product->name ?? ('Item #' . $item->id),
                        'type'        => 'Physical',
                        'quantity'    => $item->quantity,
                        'unit_price'  => [
                            'amount'   => number_format($item->price, 2, '.', ''),
                            'currency' => $order->currency ?? 'SAR',
                        ],
                        'total_amount' => [
                            'amount'   => number_format($item->price * $item->quantity, 2, '.', ''),
                            'currency' => $order->currency ?? 'SAR',
                        ],
                    ];
                })->values()->all(),
                'merchant_url' => [
                    'success'      => $this->returnUrl($order->id) . '&status=success',
                    'failure'      => $this->returnUrl($order->id) . '&status=failure',
                    'cancel'       => $this->returnUrl($order->id) . '&status=cancel',
                    'notification' => $this->webhookUrl(),
                ],
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['checkout_url'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail(data_get($data, 'message', 'Tamara error'), $data ?? []);
        }

        return GatewayInitResult::ok($data['checkout_url'], $data['order_id'] ?? null, [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withToken($this->config('api_token'))
            ->acceptJson()
            ->get($this->baseUrl() . '/orders/' . $reference);

        $data = $response->json();

        if (!$response->successful()) {
            return GatewayVerifyResult::fail(data_get($data, 'message', 'Tamara verify error'), $data ?? []);
        }

        $status = $data['status'] ?? null;

        if (in_array($status, ['approved', 'authorised', 'fully_captured'])) {
            return GatewayVerifyResult::paid(
                $reference,
                (float) data_get($data, 'total_amount.amount', 0),
                data_get($data, 'total_amount.currency', 'SAR'),
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, $status, $data);
    }

       public function handleWebhook(Request $request): GatewayVerifyResult
    {
        $token = $request->header('X-Notification-Token') ?? $request->input('notification_token');
        $expected = $this->config('notification_token');

        // ✅ التحقق الإلزامي: لو التوكن غير موجود أو غير مضبوط، ارفض الطلب
        if (!$expected || !$token || $token !== $expected) {
            return GatewayVerifyResult::fail('Invalid or missing Tamara notification token.', $request->all());
        }

        $orderId = $request->input('order_id');

        if (!$orderId) {
            return GatewayVerifyResult::fail('Missing order_id in webhook payload.', $request->all());
        }

        return $this->verify($orderId);
    }
}
