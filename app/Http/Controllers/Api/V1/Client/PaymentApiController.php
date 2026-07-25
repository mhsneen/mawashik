<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InitiatePaymentRequest;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayManager;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentApiController extends Controller
{
    use ApiTrait;

    public function __construct(protected PaymentGatewayManager $gateways)
    {
    }

    /**
     * قائمة طرق الدفع المفعّلة حالياً من لوحة التحكم (تُستخدم لعرضها في شاشة الدفع بالتطبيق).
     */
    public function get_payment_methods(Request $request)
    {
        $methods = $this->gateways->enabledMethods()->map(function ($method) {
            return [
                'code' => $method->code,
                'name' => $method->nameFor($this->getCurrentLang()),
                'mode' => $method->mode,
            ];
        })->values();

        return $this->returnData('data', $methods, __('api.Api_Return_Data'));
    }

    /**
     * إنشاء عملية دفع لطلب موجود مسبقاً. يرجع للتطبيق رابط الدفع (checkout_url) ليفتحه في WebView،
     * أو بيانات إضافية (extra) في حال احتاجت البوابة لعرض widget بدل رابط مباشر (مثال: HyperPay).
     */
    public function initiate_payment(InitiatePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();

            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                DB::rollBack();
                return $this->returnError(__('api.Order_Not_Found'));
            }

            if ($order->payment_status === 'paid') {
                DB::rollBack();
                return $this->returnError(__('api.Payment_Already_Paid'));
            }

            $code = $request->gateway;

            if (!$this->gateways->isEnabled($code)) {
                DB::rollBack();
                return $this->returnError(__('api.Payment_Method_Not_Enabled'));
            }

            $driver = $this->gateways->driver($code);
            $result = $driver->initiate($order);

            if (!$result->success) {
                Log::error('initiate_payment failed', ['order_id' => $order->id, 'gateway' => $code, 'message' => $result->message]);
                DB::rollBack();
                return $this->returnError($result->message ?: __('api.Payment_Failed'));
            }

            $order->update([
                'payment_gateway_code'      => $code,
                'payment_reference'         => $result->reference,
                'payment_status'            => 'pending',
                'payment_gateway_response'  => json_encode($result->raw),
            ]);

            DB::commit();

            return $this->returnData('data', [
                'checkout_url' => $result->redirectUrl,
                'reference'    => $result->reference,
                'extra'        => $result->extra,
            ], __('api.Payment_Initiated_Success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('initiate_payment: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * صفحة رجوع المستخدم من صفحة الدفع (Hosted Page) - يفتحها WebView داخل التطبيق.
     * نتحقق من حالة الدفع فعلياً من سيرفرات البوابة قبل اعتماد أي نتيجة (لا نثق بمعاملات الرابط فقط).
     */
    public function payment_return(Request $request, string $gateway)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if (!$order || $order->payment_gateway_code !== $gateway || !$order->payment_reference) {
            return view('site.payment.result', ['success' => false, 'message' => __('api.Order_Not_Found')]);
        }

        if ($order->payment_status === 'paid') {
            return view('site.payment.result', ['success' => true, 'message' => __('api.Payment_Success')]);
        }

        try {
            $driver = $this->gateways->driver($gateway);
            $result = $driver->verify($order->payment_reference);
        } catch (Exception $ex) {
            Log::error('payment_return verify failed: ' . $ex->getMessage(), ['order_id' => $order->id]);
            return view('site.payment.result', ['success' => false, 'message' => __('api.Payment_Failed')]);
        }

        $this->applyVerifyResult($order, $result);

        return view('site.payment.result', [
            'success' => $result->paid,
            'message' => $result->paid ? __('api.Payment_Success') : __('api.Payment_Failed'),
        ]);
    }

    /**
     * Webhook / IPN عام لكل البوابات - يجب تسجيل هذا الرابط عند كل مزوّد دفع من لوحته الخاصة:
     * {APP_URL}/api/payments/webhook/{gateway}
     */
    public function payment_webhook(Request $request, string $gateway)
    {
        try {
            $driver = $this->gateways->driver($gateway);
            $result = $driver->handleWebhook($request);
        } catch (Exception $ex) {
            Log::error('payment_webhook failed: ' . $ex->getMessage(), ['gateway' => $gateway, 'payload' => $request->all()]);
            return response()->json(['status' => 'error'], 200); // 200 حتى لا تعيد البوابة المحاولة بلا داعٍ لخطأ من طرفنا
        }

        if (!$result->reference) {
            return response()->json(['status' => 'ignored']);
        }

        $order = Order::where('payment_gateway_code', $gateway)
            ->where('payment_reference', $result->reference)
            ->first();

        if ($order) {
            $this->applyVerifyResult($order, $result);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function applyVerifyResult(Order $order, $result): void
    {
        if ($order->payment_status === 'paid') {
            return; // لا نكرر إضافة نقاط الولاء أو أي منطق مرتبط بالدفع
        }

        $order->update([
            'payment_status'            => $result->paid ? 'paid' : 'failed',
            'paid_at'                   => $result->paid ? now() : null,
            'payment_gateway_response'  => json_encode($result->raw),
        ]);
    }
}
