<?php

namespace App\Http\Requests\Api\V1\Trader;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

/**
 * طلب إضافة/تعديل منتج من قبل التاجر.
 * المنتج يُنشأ بحالة pending ويتطلب اعتماد الأدمن قبل النشر.
 */
class TraderProductRequest extends FormRequest
{
    use ApiTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|array',
            'name.ar'              => 'required|string|max:150',
            'name.en'              => 'nullable|string|max:150',

            'description'          => 'nullable|array',
            'description.ar'       => 'nullable|string|max:2000',
            'description.en'       => 'nullable|string|max:2000',

            'details'              => 'nullable|array',
            'details.ar'           => 'nullable|string|max:5000',
            'details.en'           => 'nullable|string|max:5000',

            'category_id'          => 'required|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'unit_id'              => 'nullable|exists:units,id',
            'sku'                  => 'nullable|string|max:100',

            'netPrice'             => 'required|numeric|min:0',
            'salePrice'            => 'nullable|numeric|min:0',
            'salePrice_start'      => 'nullable|date',
            'salePrice_end'        => 'nullable|date|after_or_equal:salePrice_start',

            'quantity'             => 'required|numeric|min:0',
            'track_quantity'       => 'nullable|boolean',

            'image'                => 'nullable|string',

            // خدمة الذبح
            'slaughter_available'  => 'nullable|boolean',
            'slaughter_required'   => 'nullable|boolean',
            'slaughter_price'      => 'nullable|numeric|min:0',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
