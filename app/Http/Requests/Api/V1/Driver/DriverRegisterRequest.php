<?php

namespace App\Http\Requests\Api\V1\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;
use App\Traits\NormalizesSaudiMobile;
/**
 * طلب تسجيل مندوب توصيل جديد:
 *  - بيانات شخصية: الاسم، رقم الجوال، البريد (اختياري)
 *  - صورة الهوية (مطلوبة)
 *  - صورة الرخصة (مطلوبة)
 *  - موافقة الشروط
 *
 * حالة الحساب بعد التسجيل: pending (بانتظار اعتماد الأدمن).
 * لا يمكنه قبول الطلبات حتى يتم اعتماده.
 */
class DriverRegisterRequest extends FormRequest
{
       use ApiTrait, NormalizesSaudiMobile;


    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:150',
              'mobile' => [
               'required',
                 SaudiMobileRule::make(),
               Rule::unique('users', 'mobile')->whereNull('deleted_at'),
           ],
            'email'            => 'nullable|email|max:255|unique:users,email',
            'password'         => 'required|string|min:6|max:30',
            'image'            => 'nullable|image|max:5120',
            'id_number'        => [
                'required', 'string', 'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'id_number')->whereNull('deleted_at'),
            ],
            'license_image'    => 'required|image|max:5120',
            'country_id'       => 'nullable|exists:countries,id',
            'city_id'          => 'nullable|exists:cities,id',
            'area_id'          => 'nullable|exists:areas,id',
            'neighborhood_id'  => 'nullable|exists:neighborhoods,id',
            'agreement'        => 'required|boolean|accepted',
            'device_token'     => 'nullable|string|max:255',
            'api_password'     => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.unique'          => __('api.Mobile_Already_Registered'),
            'id_number.required'     => __('api.ID_Number_Required'),
            'id_number.regex'        => __('api.ID_Number_Invalid'),
            'id_number.unique'       => __('api.ID_Number_Already_Registered'),
            'email.unique'           => __('api.Email_Already_Registered'),
            'city_id.exists'         => __('api.City_Not_Found'),
            'area_id.exists'         => __('api.Area_Not_Found'),
            'neighborhood_id.exists' => __('api.Neighborhood_Not_Found'),
            'agreement.accepted'     => __('api.Agreement_Required'),
            'license_image.required' => __('api.License_Image_Required'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'             => __('dashboard.Name'),
            'mobile'           => __('dashboard.Mobile'),
            'email'            => __('dashboard.Email'),
            'password'         => __('dashboard.Password'),
            'image'            => __('dashboard.Image'),
            'license_image'    => __('dashboard.License_Image'),
            'city_id'          => __('dashboard.City'),
            'area_id'          => __('dashboard.Area'),
            'neighborhood_id'  => __('dashboard.Neighborhood'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
