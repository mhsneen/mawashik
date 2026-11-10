<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;
use App\Rules\SaudiMobileRule;
use App\Traits\NormalizesSaudiMobile;
class ResendOtpRequest   extends FormRequest
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
     'mobile' => ['required_without:email', 'nullable', SaudiMobileRule::make()],
            'email'  => ['required_without:mobile', 'nullable', 'email'],
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


        ];
    }
}
