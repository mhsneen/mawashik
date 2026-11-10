<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ProductUpdateRequest — قواعد التحقق عند تعديل منتج.
 * نفس قواعد ProductRequest مع جعل الصورة اختيارية.
 */
class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // === الاسم والوصف ===
            'name_ar'        => 'required|max:200',
            'name_en'        => 'required|max:200',
            'description_ar' => 'nullable|string|max:2000',
            'description_en' => 'nullable|string|max:2000',
            'details_ar'     => 'nullable|string|max:5000',
            'details_en'     => 'nullable|string|max:5000',

            // === الصورة والفيديو ===
            'image'      => 'nullable|image|max:5120',
            'video_url'  => 'nullable|url|max:500',

            // === المواصفات ===
            'specifications'   => 'nullable|array',
            'specifications.*.key'   => 'required_with:specifications|string|max:100',
            'specifications.*.value'  => 'required_with:specifications|string|max:255',

            // === الحالة ===
            'is_active'    => 'required|in:0,1',
            'new_arrivals' => 'required|in:0,1',
            'product_status' => 'nullable|in:pending,approved,rejected,suspended',

            // === العلاقات ===
            'brand_id'    => 'nullable|numeric|exists:brands,id',
            'unit_id'     => 'nullable|numeric|exists:units,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'trader_id'   => 'nullable|numeric|exists:users,id',
            'sku'         => 'nullable|max:50',

            // === المخزون ===
            'track_quantity' => 'required|boolean',
            'quantity' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn() => $this->boolean('track_quantity')),
            ],

            // === السعر ===
            'netPrice'      => 'required|numeric|gt:0',
            'salePrice'     => 'nullable|required_with:salePrice_start,salePrice_end|numeric|gt:0|between:1,100',
            'salePrice_start' => 'nullable|required_with:salePrice|date|date_format:Y-m-d',
            'salePrice_end'  => 'nullable|required_with:salePrice|date|date_format:Y-m-d|after_or_equal:salePrice_start',
            'calories'       => 'nullable|string|max:200',

            // === خيار الذبح ===
            'slaughter_available' => 'nullable|boolean',
            'slaughter_required'  => 'nullable|boolean',
            'slaughter_price'     => 'nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name_ar'             => __('dashboard.Name') . ' (AR)',
            'name_en'             => __('dashboard.Name') . ' (EN)',
            'description_ar'      => __('dashboard.Description') . ' (AR)',
            'description_en'      => __('dashboard.Description') . ' (EN)',
            'details_ar'          => __('dashboard.Details') . ' (AR)',
            'details_en'          => __('dashboard.Details') . ' (EN)',
            'image'               => __('dashboard.Image'),
            'video_url'           => __('dashboard.Video_Url'),
            'specifications'      => __('dashboard.Specifications'),
            'is_active'           => __('dashboard.Efficiency_Status'),
            'new_arrivals'        => __('dashboard.new_arrivals'),
            'product_status'      => __('dashboard.Product_Status'),
            'category_id'         => __('dashboard.Product_Add_Category'),
            'brand_id'            => __('dashboard.Brands'),
            'trader_id'           => __('dashboard.Trader_Name'),
            'sku'                 => __('dashboard.Product_Code'),
            'quantity'            => __('dashboard.Quantity'),
            'track_quantity'      => __('dashboard.track_quantity'),
            'netPrice'            => __('dashboard.Base_price'),
            'salePrice'           => __('dashboard.Discount_percentage'),
            'salePrice_start'     => __('dashboard.Offer_start_date'),
            'salePrice_end'       => __('dashboard.Offer_end_date'),
            'calories'            => __('dashboard.Calories'),
            'unit_id'             => __('dashboard.Units'),
            'slaughter_available' => __('dashboard.Slaughter_Available'),
            'slaughter_required'  => __('dashboard.Slaughter_Required'),
            'slaughter_price'     => __('dashboard.Slaughter_Price'),
        ];
    }
}
