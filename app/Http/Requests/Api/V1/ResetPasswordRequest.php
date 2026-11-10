<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class ResetPasswordRequest     extends FormRequest
{
    use ApiTrait;
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
  'mobile'      => ['required_without:email', 'nullable', 'string'],
            'email'       => ['required_without:mobile', 'nullable', 'email'],
            'reset_token' => ['required', 'string'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
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

            'mobile'=>__('dashboard.Mobile'),
            'email'=>__('dashboard.Email'),
            'reset_token'=>__('dashboard.Reset_Token'),
            'password'=>__('dashboard.Password'),
            'password_confirmation'=>__('dashboard.Password_Confirmation'),


        ];
    }
}
