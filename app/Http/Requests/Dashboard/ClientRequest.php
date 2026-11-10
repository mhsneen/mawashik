<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;
/**
 * ClientRequest — قواعد التحقق لبيانات العميل.
 *
 * الحقول حسب البند الثاني من مواصفات "وسيط المواشي":
 *  - الاسم
 *  - رقم الجوال (مطلوب للتحقق OTP)
 *  - البريد الإلكتروني (اختياري)
 *  - كلمة المرور (مطلوبة عند الإنشاء، اختيارية عند التعديل)
 *  - الصورة الشخصية
 *  - الموقع الجغرافي (country/city/area/neighborhood)
 *  - الموافقة على الشروط
 *  - الحالة الفعالة + حالة الحظر
 */
class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // البيانات الشخصية
            'name'      => 'required|string|max:150',
            'is_active' => 'required|in:0,1',
            'is_blocked'=> 'nullable|in:0,1',
            'image'     => 'nullable|image|max:5120',

            // رقم الجوال — قاعدة سعودية موحّدة + فريد بدون حساب السجل الحالي
            'mobile' => [
                'required',
                SaudiMobileRule::make(),
                Rule::unique('users', 'mobile')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],


            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],

            // كلمة المرور — مطلوبة عند الإنشاء، اختيارية عند التعديل
            /*'password' => in_array($this->method(), ['PUT', 'PATCH'])
                ? 'nullable|string|min:6'
                : 'required|string|min:6',*/

            // === الموقع الجغرافي ===
            'country_id'      => 'nullable|exists:countries,id',
            'city_id'         => 'nullable|exists:cities,id',
            'area_id'         => 'nullable|exists:areas,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',

            // الموافقة على الشروط
            'terms_accepted' => in_array($this->method(), ['PUT', 'PATCH'])
                ? 'nullable|boolean'
                : 'required|accepted',

            // الإشعارات
            'notifications_enabled' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => __('dashboard.Name'),
            'image'          => __('dashboard.Image'),
            'is_active'      => __('dashboard.Efficiency_Status'),
            'is_blocked'     => __('dashboard.Block_Status'),
            'mobile'         => __('dashboard.Mobile'),
            'email'          => __('dashboard.Email'),
            'password'       => __('dashboard.Password'),
            'country_id'     => __('dashboard.Country'),
            'city_id'        => __('dashboard.City'),
            'area_id'        => __('dashboard.Area'),
            'neighborhood_id'=> __('dashboard.Neighborhood'),
            'terms_accepted' => __('dashboard.Terms_Accepted'),
            'notifications_enabled' => __('dashboard.Notifications_Enabled'),
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => __('dashboard.Terms_Must_Be_Accepted'),
        ];
    }
}
