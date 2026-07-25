<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class CalculateDeliveryPriceRequest extends FormRequest
{
    use ApiTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'address_id' => 'nullable|integer|exists:addresses,id',
            'latitude' => 'required_without:address_id|nullable|numeric|between:-90,90',
            'longitude' => 'required_without:address_id|nullable|numeric|between:-180,180',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
