<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantsRequest extends FormRequest
{
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
            'variants' => 'required|array|min:1',
            'variants.*.value_ids' => 'required|string',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.quantity' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'variants.*.sku' => __('dashboard.SKU'),
            'variants.*.price' => __('dashboard.Price'),
            'variants.*.quantity' => __('dashboard.Quantity'),
        ];
    }
}
