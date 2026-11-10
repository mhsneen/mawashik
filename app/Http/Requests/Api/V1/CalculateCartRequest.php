<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class CalculateCartRequest extends FormRequest
{
    use ApiTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer|exists:products,id',
            'cart.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'cart.*.modifier_value_ids' => 'nullable|array',
            'cart.*.modifier_value_ids.*' => 'integer|exists:attribute_values,id',
            'cart.*.quantity' => 'required|integer|min:1',

            // اختياري: لمعاينة سعر التوصيل ضمن نفس المعاينة
            'address_id' => 'nullable|integer|exists:addresses,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'redeem_points' => 'nullable|integer|min:0',
                        'cart.*.slaughter_selected' => 'nullable|boolean',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }

    public function attributes()
    {
        return [
            'cart' => __('dashboard.Cart'),
             'redeem_points'=>__('dashboard.redeem_points'),
        ];
    }
}
