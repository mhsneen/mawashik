<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class UpdatePasswordRequest extends FormRequest
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
            'oldpassword'=>'required|min:6|current_password',
            'password'=>'required|min:6|different:oldpassword',
            'password_confirmation' => 'required|min:6|same:password',

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

            'oldpassword'=>__('dashboard.Old_Password'),
            'oldpassword.current_password'=>__('dashboard.Old_Password'),
            'password' => __('dashboard.Password'),
            'password_confirmation' => __('dashboard.Confirm_Password'),

        ];
    }
}
