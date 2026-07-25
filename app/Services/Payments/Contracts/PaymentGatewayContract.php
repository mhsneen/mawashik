<?php

namespace App\Services\Payments\Contracts;

use App\Models\Order;
use App\Services\Payments\DTO\GatewayInitResult;
use App\Services\Payments\DTO\GatewayVerifyResult;
use Illuminate\Http\Request;

interface PaymentGatewayContract
{
    /**
     * إنشاء عملية دفع جديدة عند البوابة وإرجاع رابط/بيانات الدفع للتطبيق.
     */
    public function initiate(Order $order, array $options = []): GatewayInitResult;

    /**
     * التحقق من حالة عملية دفع معيّنة مباشرة من سيرفرات البوابة (Server-to-Server)
     * تُستخدم عند رجوع المستخدم من صفحة الدفع (return_url) للتأكد الفعلي قبل تحديث الطلب.
     */
    public function verify(string $reference): GatewayVerifyResult;

    /**
     * معالجة إشعار Webhook القادم من البوابة (IPN) والتحقق من توقيعه.
     */
    public function handleWebhook(Request $request): GatewayVerifyResult;

    /** كود البوابة كما هو مسجّل في config/payment_gateways.php وجدول payment_methods */
    public function code(): string;
}
