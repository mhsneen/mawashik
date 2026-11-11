<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MyFatoorahGateway extends AbstractPaymentGateway
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://apitest.myfatoorah.com'
            : 'https://api.myfatoorah.com';
    }

    public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $response = Http::withToken($this->config('api_key'))
            ->acceptJson()
            ->post($this->baseUrl() . '/v2/SendPayment', [
                'CustomerName'      => $order->user->name ?? 'Customer',
                'NotificationOption' => 'LNK',
                'InvoiceValue'      => $order->online_amount,
                'CustomerReference' => (string) $order->id,
                'CallBackUrl'       => $this->returnUrl($order->id) . '&status=success',
                'ErrorUrl'          => $this->returnUrl($order->id) . '&status=error',
                'DisplayCurrencyIso' => $order->currency ?? 'SAR',
            ]);

        $data = $response->json();

        if (!$response->successful() || !($data['IsSuccess'] ?? false)) {
            $this->logError('initiate failed', $data ?? []);
            return GatewayInitResult::fail($data['Message'] ?? 'MyFatoorah error', $data ?? []);
        }

        $invoiceUrl = data_get($data, 'Data.InvoiceURL');
        $invoiceId  = data_get($data, 'Data.InvoiceId');

        return GatewayInitResult::ok($invoiceUrl, (string) $invoiceId, [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withToken($this->config('api_key'))
            ->acceptJson()
            ->post($this->baseUrl() . '/v2/GetPaymentStatus', [
                'Key'     => $reference,
                'KeyType' => 'InvoiceId',
            ]);

        $data = $response->json();

        if (!$response->successful() || !($data['IsSuccess'] ?? false)) {
            return GatewayVerifyResult::fail($data['Message'] ?? 'MyFatoorah verify error', $data ?? []);
        }

        $status = data_get($data, 'Data.InvoiceStatus');

        if ($status === 'Paid') {
            return GatewayVerifyResult::paid(
                $reference,
                (float) data_get($data, 'Data.InvoiceValue'),
                data_get($data, 'Data.InvoiceDisplayCurrencyIso', 'SAR'),
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, $status, $data);
    }

    public function handleWebhook(Request $request): GatewayVerifyResult
    {
        // ماي فاتورة ترسل InvoiceId ضمن الاستدعاء - نعيد التحقق مباشرة من سيرفراتهم
        $invoiceId = $request->input('InvoiceId') ?? $request->input('Data.InvoiceId');

        if (!$invoiceId) {
            return GatewayVerifyResult::fail('Missing InvoiceId in webhook payload.', $request->all());
        }

        return $this->verify((string) $invoiceId);
    }
}
