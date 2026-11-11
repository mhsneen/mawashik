<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;

/**
 * DriverRequest — قواعد التحقق لبيانات مندوب التوصيل (السائق).
 *
 * الحقول المطلوبة حسب البند الرابع من مواصفات "وسيط المواشي":
 *  - البيانات الشخصية: name, mobile, email, image (الصورة الشخصية)
 *  - رخصة السائق: id_number (رقم الهوية), license_image (صورة الرخصة)
 *  - الموافقة على الشروط: terms_accepted
 *  - حالة الاعتماد الإدارية: driver_status (pending/approved/rejected/suspended)
 *  - نسبة عمولة السائق: driver_commission_rate
 *  - الحالة الأساسية: is_active
 *
 * ملاحظة: عند التعديل (update) تكون صور الهوية والرخصة اختيارية
 * لأنها قد تكون مرفوعة مسبقاً.
 */
class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
$isUpdate = $this->filled('id');
        return [
            // البيانات الشخصية
            'name'                => 'required|string|max:150',
            'is_active'           => 'required|in:0,1',
            'image'               => 'nullable|image|max:5120', // الصورة الشخصية (5MB)

            // رقم الجوال — قاعدة سعودية موحّدة + فريد بدون حساب السجل الحالي
            'mobile' => [
                'required',
                SaudiMobileRule::make(),
                Rule::unique('users', 'mobile')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],

            // البريد الإلكتروني — اختياري
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],

            // كلمة المرور — مطلوبة عند الإنشاء، اختيارية عند التعديل
          //  'password' => $isUpdate ? 'nullable|string|min:6' : 'required|string|min:6',

            // === حقول مندوب التوصيل الجديدة (حسب الـ PDF) ===

            // رقم الهوية (بدون صورة هوية حسب المتطلبات)
            'id_number' => [
                'required', 'string', 'regex:/^[0-9]{10}$/',
                \Illuminate\Validation\Rule::unique('users', 'id_number')->whereNull('deleted_at')->ignore($this->id),
            ],

            // صورة الرخصة
            'license_image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'max:5120', // 5MB
            ],

            // الموافقة على الشروط — مطلوبة عند الإنشاء فقط
            'terms_accepted' => $isUpdate ? 'nullable|boolean' : 'required|accepted',

            // حالة اعتماد السائق — يستطيع الأدمن تعديلها يدوياً
            'driver_status' => 'nullable|in:pending,approved,rejected,suspended',

            // ملاحظة الأدمن على حالة الاعتماد (سبب رفض/إيقاف)
            'driver_status_note' => 'nullable|string|max:500',

            // نسبة عمولة السائق — اختيارية (لو فارغة تأخذ الافتراضي من الإعدادات)
            'driver_commission_rate' => 'nullable|numeric|between:0,100',

            // الإشعارات
            'notifications_enabled' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                    => __('dashboard.Name'),
            'image'                   => __('dashboard.Image'),
            'is_active'               => __('dashboard.Efficiency_Status'),
            'mobile'                  => __('dashboard.Mobile'),
            'email'                   => __('dashboard.Email'),
         //   'password'                => __('dashboard.Password'),
            'id_number'               => __('dashboard.ID_Number'),
            'license_image'           => __('dashboard.License_Image'),
            'terms_accepted'          => __('dashboard.Terms_Accepted'),
            'driver_status'           => __('dashboard.Driver_Status'),
            'driver_status_note'      => __('dashboard.Driver_Status_Note'),
            'driver_commission_rate'  => __('dashboard.Driver_Commission_Rate'),
            'notifications_enabled'   => __('dashboard.Notifications_Enabled'),
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => __('dashboard.Terms_Must_Be_Accepted'),
            'license_image.required'  => __('dashboard.License_Image_Required'),
        ];
    }
}
