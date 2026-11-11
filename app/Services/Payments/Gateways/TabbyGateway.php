<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TabbyGateway extends AbstractPaymentGateway
{
    protected string $baseUrl = 'https://api.tabby.ai';

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $user = $order->user;

        $response = Http::withToken($this->config('secret_key'))
            ->post($this->baseUrl . '/api/v2/checkout', [
                'payment' => [
                    'amount'      => number_format($order->online_amount, 2, '.', ''),
                    'currency'    => $order->currency ?? 'SAR',
                    'description' => 'Order #' . $order->id,
                    'buyer' => [
                        'phone' => $user->mobile ?? '',
                        'email' => $user->email ?? 'guest@example.com',
                        'name'  => $user->name ?? 'Customer',
                    ],
                    // موصى بها من Tabby لتحسين معدل قبول الطلب (تقييم المخاطر)، ليست إلزامية
                    // لكن غيابها يقلل فرصة الموافقة خصوصاً في وضع Live.
                    'buyer_history' => [
                        'registered_since' => optional($user->created_at)->toIso8601String() ?? now()->toIso8601String(),
                        'loyalty_level'    => 0,
                    ],
                    'order' => [
                        'reference_id' => (string) $order->id,
                        'items'        => $order->items->map(function ($item) {
                            return [
                                'title'    => $item->product->name ?? ('Item #' . $item->id),
                                'quantity' => $item->quantity,
                                'unit_price' => number_format($item->price, 2, '.', ''),
                            ];
                        })->values()->all(),
                    ],
                ],
                'merchant_code' => $this->config('merchant_code'),
                'merchant_urls' => [
                    'success' => $this->returnUrl($order->id) . '&status=success',
                    'cancel'  => $this->returnUrl($order->id) . '&status=cancel',
                    'failure' => $this->returnUrl($order->id) . '&status=failure',
                ],
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['id'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail(data_get($data, 'error', 'Tabby error'), $data ?? []);
        }

        $webUrl = data_get($data, 'configuration.available_products.installments.0.web_url');

        // ملاحظة مهمة: id على المستوى الأعلى هو معرف الـ Session، بينما payment.id هو معرف
        // الدفعة الفعلي المستخدم في verify()/webhook عبر GET /api/v2/payments/{id}. يجب تخزين
        // هذا الأخير كمرجع للطلب، وإلا لن تُطابق أي عملية تحقق أو webhook لاحقة الطلب الصحيح.
        $paymentId = data_get($data, 'payment.id', $data['id']);

        return GatewayInitResult::ok($webUrl, $paymentId, [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withToken($this->config('secret_key'))
            ->get($this->baseUrl . '/api/v2/payments/' . $reference);

        $data = $response->json();

        if (!$response->successful()) {
            return GatewayVerifyResult::fail(data_get($data, 'error', 'Tabby verify error'), $data ?? []);
        }

        $status = $data['status'] ?? null;

        if (in_array($status, ['AUTHORIZED', 'CLOSED'])) {
            return GatewayVerifyResult::paid(
                $reference,
                (float) data_get($data, 'amount', 0),
                data_get($data, 'currency', 'SAR'),
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, $status, $data);
    }

    public function handleWebhook(Request $request): GatewayVerifyResult
    {
        $paymentId = $request->input('id');

        if (!$paymentId) {
            return GatewayVerifyResult::fail('Missing payment id in webhook payload.', $request->all());
        }

        return $this->verify($paymentId);
    }
}
