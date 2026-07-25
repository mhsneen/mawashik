<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class MessageContactsRequest extends FormRequest
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
            'name' => 'required|max:100',
            'mobile' => 'required|numeric|digits:10',
            'details' => 'required|max:300',
            'email' => 'required|email|max:100',
            'subject' => 'required|max:100',

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
            'name' => __('dashboard.Name'),
            'mobile' => __('dashboard.Mobile'),
            'details' => __('dashboard.popup_text'),
            'email' => __('dashboard.Email'),
            'subject' => __('dashboard.Subject'),

        ];
    }
}
