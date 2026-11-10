<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class SmsService
{
    public function send(string $mobile, string $message): bool
    {
        // جلب الإعدادات الخاصة بالـ SMS من الداتابيز
        $settings = Setting::whereIn('key', [
            'sms_provider', 'sms_api_key', 'sms_sender_id',
            'sms_username', 'sms_password', 'sms_base_url'
        ])->pluck('plain_value', 'key');

        $baseUrl = $settings['sms_base_url'] ?? '';
        $apiKey  = $settings['sms_api_key'] ?? '';
        $sender  = $settings['sms_sender_id'] ?? '';
        $user    = $settings['sms_username'] ?? '';
        $pass    = $settings['sms_password'] ?? '';

        try {
            // مثال على كيفية الإرسال (يجب تعديل الحقول حسب مزود الخدمة الخاص بك)
            $response = Http::asForm()->post($baseUrl, [
                'api_id'   => $apiKey,
                'user'     => $user,
                'pass'     => $pass,
                'sender'   => $sender,
                'to'       => $mobile,
                'msg'      => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("SMS Sending Failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("SMS Exception: " . $e->getMessage());
            return false;
        }
    }
}
