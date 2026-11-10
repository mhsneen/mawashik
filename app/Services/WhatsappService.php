<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class WhatsappService
{
    public function send(string $mobile, string $message): bool
    {
        // جلب إعدادات الواتساب
        $settings = Setting::whereIn('key', [
            'whatsapp_provider', 'whatsapp_api_url', 'whatsapp_token',
            'whatsapp_instance_id', 'whatsapp_phone_number_id', 'whatsapp_business_account_id'
        ])->pluck('plain_value', 'key');

        $apiUrl   = $settings['whatsapp_api_url'] ?? '';
        $token    = $settings['whatsapp_token'] ?? '';
        $phoneId  = $settings['whatsapp_phone_number_id'] ?? '';

        try {
            // مثال لو كنت تستخدم WhatsApp Cloud API الرسمي
            $response = Http::withToken($token)
                ->post("$apiUrl/$phoneId/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $mobile,
                    'type'              => 'text',
                    'text'              => ['body' => $message]
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("WhatsApp Sending Failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp Exception: " . $e->getMessage());
            return false;
        }
    }
}
