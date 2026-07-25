<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class NotificationsRequest extends FormRequest
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
            'notification_id' => 'required|numeric|exists:notifications,id',

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
            'notification_id' => __('api.Notification'),


        ];
    }
}
