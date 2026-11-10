<?php

namespace App\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * @mixin FormRequest
 */
trait NormalizesSaudiMobile
{
    /**
     * حقول تسجيل الدخول التي قد تحتوي على رقم جوال بدل الإيميل.
     */
    protected array $loginFields = ['email', 'login'];

    /**
     * حقول الجوال العادية التي يجب تطبيعها.
     */
    protected array $mobileFields = ['mobile', 'whatsapp_number'];

    protected function prepareForValidation()
    {
        // 1) تطبيع حقول الجوال العادية
        foreach ($this->mobileFields as $field) {
            if ($this->filled($field)) {
                $this->merge([
                    $field => $this->toE164($this->input($field)),
                ]);
            }
        }

        // 2) تطبيع حقول تسجيل الدخول (email/login) إذا كانت تحتوي رقم جوال وليست إيميل
        foreach ($this->loginFields as $field) {
            if ($this->filled($field)) {
                $value = $this->input($field);
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->merge([
                        $field => $this->toE164($value),
                    ]);
                }
            }
        }
    }

    /**
     * تحويل أي صيغة رقم سعودي إلى E.164.
     */
    protected function toE164(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        try {
            return (string) (new PhoneNumber($value, 'SA'))->formatE164();
        } catch (\Exception $e) {
            // fallback يدوي
            $normalized = preg_replace('/[^0-9]/', '', $value);

            if (str_starts_with($normalized, '00')) {
                $normalized = substr($normalized, 2);
            } elseif (str_starts_with($normalized, '0')) {
                $normalized = '966' . substr($normalized, 1);
            } elseif (strlen($normalized) === 9 && !str_starts_with($normalized, '966')) {
                $normalized = '966' . $normalized;
            }

            return '+' . $normalized;
        }
    }
}
