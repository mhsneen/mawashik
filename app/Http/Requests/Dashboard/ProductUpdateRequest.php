<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
            'name_ar' => 'required|max:200',
            'name_en' => 'required|max:200',
            'image' => 'required_without:id|image',
            'is_active' => 'required|in:0,1',
            'new_arrivals' => 'required|in:0,1',
            'brand_id' => 'nullable|numeric|exists:brands,id',
            'sku' => 'nullable|max:50',
             'track_quantity' => 'required|boolean',
        'quantity' => [
            'nullable',
            'numeric',
            'min:0',
            Rule::requiredIf(fn() => $this->boolean('track_quantity')),
        ],
'unit_id' => 'nullable|numeric|exists:units,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'netPrice' => 'required|numeric|gt:0',
            'salePrice' => 'nullable|required_with:salePrice_start,salePrice_end|numeric|gt:0|between:1,100',
            'salePrice_start' => 'nullable|required_with:salePrice|date|date_format:Y-m-d',
            'salePrice_end' => 'nullable|required_with:salePrice|date|date_format:Y-m-d|after_or_equal:salePrice_start',
            'calories' => 'nullable|String|max:200',

        ];
    }


    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.Name'),
            'name_en' => __('dashboard.Name'),
            'is_active' => __('dashboard.Efficiency_Status'),
            'new_arrivals' => __('dashboard.new_arrivals'),
            'image' => __('dashboard.Image'),
            'category_id' => __('dashboard.Product_Add_Category'),
            'brand_id' => __('dashboard.Brands'),
            'sku' => __('dashboard.Product_Code'),
            'quantity' => __('dashboard.Quantity'),
            'netPrice' => __('dashboard.Base_price'),
            'salePrice_start' => __('dashboard.Offer_start_date'),
            'salePrice_end' => __('dashboard.Offer_end_date'),
            'salePrice' => __('dashboard.Discount_percentage'),
            'calories' => __('dashboard.Calories'),
            'unit_id' => __('dashboard.Units'),

'track_quantity' => __('dashboard.track_quantity'),

        ];
    }
}
