<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayTabsGateway extends AbstractPaymentGateway
{
    protected function baseUrl(): string
    {
        return rtrim($this->config('region_url', 'https://secure.paytabs.sa'), '/');
    }

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $response = Http::withHeaders(['Authorization' => $this->config('server_key')])
            ->post($this->baseUrl() . '/payment/request', [
                'profile_id'      => $this->config('profile_id'),
                'tran_type'       => 'sale',
                'tran_class'      => 'ecom',
                'cart_id'         => (string) $order->id,
                'cart_currency'   => $order->currency ?? 'SAR',
                'cart_amount'     => (float) $order->online_amount,
                'cart_description' => 'Order #' . $order->id,
                'return'          => $this->returnUrl($order->id),
                'callback'        => $this->webhookUrl(),
                'customer_details' => [
                    'name'    => $order->user->name ?? 'Customer',
                    'email'   => $order->user->email ?? 'guest@example.com',
                    'phone'   => $order->user->mobile ?? '',
                    'street1' => $order->address ?? '-',
                    'city'    => '-',
                    'state'   => '-',
                    'country' => 'SA',
                    'zip'     => '00000',
                ],
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['redirect_url'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail($data['message'] ?? 'PayTabs error', $data ?? []);
        }

        return GatewayInitResult::ok($data['redirect_url'], $data['tran_ref'] ?? null, [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withHeaders(['Authorization' => $this->config('server_key')])
            ->post($this->baseUrl() . '/payment/query', [
                'profile_id' => $this->config('profile_id'),
                'tran_ref'   => $reference,
            ]);

        $data = $response->json();

        if (!$response->successful()) {
            return GatewayVerifyResult::fail($data['message'] ?? 'PayTabs verify error', $data ?? []);
        }

        $status = data_get($data, 'payment_result.response_status');

        if ($status === 'A') { // Authorized/Approved
            return GatewayVerifyResult::paid(
                $reference,
                (float) data_get($data, 'cart_amount', 0),
                data_get($data, 'cart_currency', 'SAR'),
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, data_get($data, 'payment_result.response_message'), $data);
    }

    public function handleWebhook(Request $request): GatewayVerifyResult
    {
        $tranRef = $request->input('tran_ref');

        if (!$tranRef) {
            return GatewayVerifyResult::fail('Missing tran_ref in webhook payload.', $request->all());
        }

        // نتحقق من سيرفرات PayTabs مباشرة بدلاً من الثقة بالـ payload وحده
        return $this->verify($tranRef);
    }
}
