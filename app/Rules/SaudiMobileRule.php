<?php

namespace App\Rules;

use Propaganistas\LaravelPhone\Rules\Phone;

/**
 * قاعدة موحّدة للتحقق من رقم جوال سعودي صحيح.
 *
 * تقبل الرقم سواء كُتب بصيغة:
 *  - محلية:        05xxxxxxxx
 *  - دولية كاملة:  +9665xxxxxxxx
 *  - دولية بدون +: 9665xxxxxxxx
 *
 * الاستخدام في FormRequest:
 *
 *   use App\Rules\SaudiMobileRule;
 *   use Illuminate\Validation\Rule;
 *
 *   'mobile' => [
 *       'required',
 *       SaudiMobileRule::make(),
 *       Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore($this->id),
 *   ],
 *
 * ملاحظة: القاعدة تُرجع كائن Phone (مش string) — لذلك تُمرَّر بدون 'string' أو قوسين.
 */
class SaudiMobileRule
{
    /**
     * بناء قاعدة التحقق لرقم جوال سعودي.
     *
     * @return \Propaganistas\LaravelPhone\Rules\Phone
     */
    public static function make(): Phone
    {
        return (new Phone())->country('SA');
    }
}
