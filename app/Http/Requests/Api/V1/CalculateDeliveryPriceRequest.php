<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class CalculateDeliveryPriceRequest extends FormRequest
{
    use ApiTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trader_id'  => 'required|exists:users,id',
            'address_id' => 'nullable|exists:addresses,id',
            'latitude'   => 'required_without:address_id|nullable|numeric|between:-90,90',
            'longitude'  => 'required_without:address_id|nullable|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'trader_id.required' => __('api.Trader_ID_Required'),
            'trader_id.exists'   => __('api.Trader_Not_Found'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
