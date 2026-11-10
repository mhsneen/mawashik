<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;
use App\Traits\NormalizesSaudiMobile;
class LoginRequest extends FormRequest
{
     use ApiTrait, NormalizesSaudiMobile;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
           return [



         'mobile' => [
              'required_without:email',
              'prohibits:email',
              'nullable',
               SaudiMobileRule::make(), // ✅ بدل الـ regex
               Rule::exists('users', 'mobile')->whereNull('deleted_at'),
           ],


          'email'        => [
              'required_without:mobile',
              'prohibits:mobile',
              'nullable',
              'email',
              'max:255',
              Rule::exists('users', 'email')->whereNull('deleted_at'),
          ],
          'device_token' => 'nullable|string',
      ];
    }

    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(
            $this->returnError(
                $validator->errors()->first()
            )
        );
    }
    public function attributes()
    {
        return [
    'mobile'       => __('dashboard.Mobile'),
    'email'        => __('dashboard.Email'),
    'device_token' => __('dashboard.Device_Token'),


        ];
    }
}
