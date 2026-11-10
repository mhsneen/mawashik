<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;

/**
 * TraderRequest — قواعد التحقق لبيانات التاجر.
 *
 * الحقول المطلوبة حسب البند الثالث من مواصفات "وسيط المواشي":
 *  - اسم المتجر / التاجر
 *  - رقم الجوال
 *  - السجل التجاري
 *  - موقع المتجر على الخريطة (store_lat, store_lng)
 *  - نص العنوان (store_address)
 *  - الموافقة على الشروط
 *  - حالة الاعتماد (trader_status)
 *  - نسبة العمولة (commission_rate)
 *
 * ملاحظة: عند التعديل تكون صورة المتجر اختيارية.
 */
class TraderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            // البيانات الأساسية للتاجر / المتجر
            'name'      => 'required|string|max:150',
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
           // 'password' => $isUpdate ? 'nullable|string|min:6' : 'required|string|min:6',

            // الحالة الفعالة
            'is_active' => 'required|in:0,1',

            // صورة التاجر (شعار المتجر)
            'image' => 'nullable|image|max:5120',

            // === الحقول الخاصة بالتاجر ===
            'commercial_register' => 'nullable|string|max:100',

            // موقع المتجر على الخريطة
            'store_lat' => 'nullable|numeric|between:-90,90',
            'store_lng' => 'nullable|numeric|between:-180,180',
            'store_address' => 'nullable|string|max:500',

            // الجغرافيا
            'country_id'      => 'nullable|exists:countries,id',
            'city_id'         => 'nullable|exists:cities,id',
            'area_id'         => 'nullable|exists:areas,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',

            // الموافقة على الشروط
            'terms_accepted' => $isUpdate ? 'nullable|boolean' : 'required|accepted',

            // === إعدادات الاعتماد والعمولة ===
            'trader_status'    => 'nullable|in:pending,approved,rejected,suspended',
            'admin_status_note' => 'nullable|string|max:500',
            'commission_rate'  => 'nullable|numeric|between:0,100',

            // الإشعارات
            'notifications_enabled' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => __('dashboard.Trader_Name'),
            'mobile'                => __('dashboard.Mobile'),
            'email'                 => __('dashboard.Email'),
           // 'password'              => __('dashboard.Password'),
            'is_active'             => __('dashboard.Efficiency_Status'),
            'image'                 => __('dashboard.Image'),
            'commercial_register'  => __('dashboard.Commercial_Register'),
            'store_lat'             => __('dashboard.Store_Lat'),
            'store_lng'             => __('dashboard.Store_Lng'),
            'store_address'         => __('dashboard.Store_Address'),
            'country_id'            => __('dashboard.Country'),
            'city_id'               => __('dashboard.City'),
            'area_id'               => __('dashboard.Area'),
            'neighborhood_id'       => __('dashboard.Neighborhood'),
            'terms_accepted'        => __('dashboard.Terms_Accepted'),
            'trader_status'         => __('dashboard.Trader_Status'),
            'admin_status_note'     => __('dashboard.Admin_Note'),
            'commission_rate'       => __('dashboard.Commission_Rate'),
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => __('dashboard.Terms_Must_Be_Accepted'),
        ];
    }
}
