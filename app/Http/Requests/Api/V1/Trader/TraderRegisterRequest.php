<?php

namespace App\Http\Requests\Api\V1\Trader;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;
use App\Traits\NormalizesSaudiMobile;
/**
 * طلب تسجيل تاجر جديد:
 *  - بيانات المتجر: الاسم، رقم الجوال، الموقع، السجل التجاري
 *  - موافقة الشروط
 *
 * حالة الحساب بعد التسجيل: pending (بانتظار اعتماد الأدمن).
 */
class TraderRegisterRequest extends FormRequest
{
      use ApiTrait, NormalizesSaudiMobile;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:150',
           'mobile' => [
               'required',
                 SaudiMobileRule::make(),
               Rule::unique('users', 'mobile')->whereNull('deleted_at'),
           ],
            'password'             => 'required|string|min:6|max:30',
            'id_number'            => [
                'required', 'string', 'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'id_number')->whereNull('deleted_at'),
            ],
            'commercial_register'  => 'nullable|string|max:100',
            'store_lat'            => 'required|numeric|between:-90,90',
            'store_lng'            => 'required|numeric|between:-180,180',
            'store_address'        => 'required|string|max:255',
            'city_id'              => 'required|exists:cities,id',
            'area_id'              => 'nullable|exists:areas,id',
            'neighborhood_id'      => 'nullable|exists:neighborhoods,id',
            'agreement'            => 'required|boolean|accepted',
            'api_password'         => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.unique'          => __('api.Mobile_Already_Registered'),
            'id_number.required'     => __('api.ID_Number_Required'),
            'id_number.regex'        => __('api.ID_Number_Invalid'),
            'id_number.unique'       => __('api.ID_Number_Already_Registered'),
            'store_lat.between'      => __('api.Invalid_Latitude'),
            'store_lng.between'      => __('api.Invalid_Longitude'),
            'city_id.exists'         => __('api.City_Not_Found'),
            'area_id.exists'         => __('api.Area_Not_Found'),
            'neighborhood_id.exists' => __('api.Neighborhood_Not_Found'),
            'agreement.accepted'     => __('api.Agreement_Required'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                => __('dashboard.Trader_Name'),
            'mobile'              => __('dashboard.Mobile'),
            'password'            => __('dashboard.Password'),
            'commercial_register' => __('dashboard.Commercial_Register'),
            'store_lat'           => __('dashboard.Store_Lat'),
            'store_lng'           => __('dashboard.Store_Lng'),
            'store_address'       => __('dashboard.Store_Address'),
            'city_id'             => __('dashboard.City'),
            'area_id'             => __('dashboard.Area'),
            'neighborhood_id'     => __('dashboard.Neighborhood'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
