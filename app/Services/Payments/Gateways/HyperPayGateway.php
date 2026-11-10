<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\AbstractPaymentGateway;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * HyperPay تعمل بأسلوب "Copy & Pay Widget": نطلب checkoutId من السيرفر،
 * ثم يعرض التطبيق صفحة تحوي سكربت الويدجت (paymentWidgets.js?checkoutId=...) داخل WebView،
 * وبعد إدخال بيانات البطاقة تتحقق من النتيجة عبر verify().
 */
class HyperPayGateway extends AbstractPaymentGateway
{
    protected function baseUrl(): string
    {
        return rtrim($this->config('base_url', 'https://eu-test.oppwa.com'), '/');
    }

      public function initiate(Order $order, array $options = []): GatewayInitResult
    {
        $response = Http::asForm()
            ->withToken($this->config('access_token'))
            ->post($this->baseUrl() . '/v1/checkouts', [
                'entityId'              => $this->config('entity_id'),
                'amount'                => number_format($order->final_price, 2, '.', ''),
                'currency'              => $order->currency ?? 'SAR',
                'paymentType'           => 'DB',
                'merchantTransactionId' => (string) $order->id,
                'customer.email'        => $order->user->email ?? 'guest@example.com',
                'shopperResultUrl'      => $this->returnUrl($order->id),
            ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['id'])) {
            // ✅✅✅ تعديل هنا: طباعة الرد الخام لمعرفة السبب الحقيقي ✅✅✅
            $this->logError('initiate failed', ['raw_response' => $response->body()]);
            
            return GatewayInitResult::fail(data_get($data, 'result.description', 'HyperPay error'), $data ?? []);
        }

        $checkoutId = $data['id'];

        // ✅ إنشاء رابط صفحة الدفع (Hosted Page) التي أنشأناها
        $checkoutUrl = route('api.payments.hyperpay.form', [
            'checkout_id' => $checkoutId,
            'result_url'  => $this->returnUrl($order->id)
        ]);

        // ✅ إرجاع الرابط للتطبيق ليفتحه في المتصفح
        return GatewayInitResult::ok($checkoutUrl, $checkoutId, [], $data);
    }

    public function verify(string $reference): GatewayVerifyResult
    {
        $response = Http::withToken($this->config('access_token'))
            ->get($this->baseUrl() . "/v1/checkouts/{$reference}/payment", [
                'entityId' => $this->config('entity_id'),
            ]);

        $data = $response->json();
        $code = data_get($data, 'result.code', '');

        // أكواد النجاح حسب توثيق HyperPay: 000.000.* أو 000.100.1*
        $isSuccess = (bool) preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $code);

        if ($isSuccess) {
            return GatewayVerifyResult::paid(
                $reference,
                (float) ($data['amount'] ?? 0),
                $data['currency'] ?? 'SAR',
                $data
            );
        }

        return GatewayVerifyResult::notPaid($reference, data_get($data, 'result.description'), $data ?? []);
    }

    public function handleWebhook(Request $request): GatewayVerifyResult
    {
        // HyperPay لا تستخدم Webhook تقليدي بل التحقق يتم عبر verify() بعد رجوع المستخدم من الويدجت
        $checkoutId = $request->input('id') ?? $request->input('resourcePath');

        if (!$checkoutId) {
            return GatewayVerifyResult::fail('Missing checkout id.', $request->all());
        }

        return $this->verify($checkoutId);
    }
}
