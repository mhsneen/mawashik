<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MoyasarGateway extends AbstractPaymentGateway
{
    protected string $baseUrl = 'https://api.moyasar.com/v1';

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $response = Http::withBasicAuth($this->config('secret_key'), '')
            ->post($this->baseUrl . '/invoices', [
                'amount'       => $this->toMinorUnits($order->online_amount),
                'currency'     => strtoupper($order->currency ?? 'SAR'),
                'description'  => 'Order #' . $order->id,
                'callback_url' => $this->returnUrl($order->id),
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['id'])) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail($data['message'] ?? 'Moyasar error', $data ?? []);
        }

        return GatewayInitResult::ok($data['url'] ?? null, $data['id'], [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withBasicAuth($this->config('secret_key'), '')
            ->get($this->baseUrl . '/invoices/' . $reference);

        $data = $response->json();

        if (!$response->successful()) {
            return GatewayVerifyResult::fail($data['message'] ?? 'Moyasar verify error', $data ?? []);
        }

        if (($data['status'] ?? null) === 'paid') {
            return GatewayVerifyResult::paid(
                $reference,
                ($data['amount'] ?? 0) / 100,
                $data['currency'] ?? 'SAR',
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, $data['status'] ?? null, $data);
    }

    public function handleWebhook(Request $request): GatewayVerifyResult
    {
        // موياسر لا يوقّع الطلبات دائماً - الأسلم إعادة الاستعلام عن الفاتورة من سيرفراتهم مباشرة
        $invoiceId = $request->input('id') ?? data_get($request->all(), 'data.id');

        if (!$invoiceId) {
            return GatewayVerifyResult::fail('Missing invoice id in webhook payload.', $request->all());
        }

        return $this->verify($invoiceId);
    }
}
