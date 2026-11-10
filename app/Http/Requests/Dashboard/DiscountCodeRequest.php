<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountCodeRequest extends FormRequest
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
         'code' => [
    'required',
    'string',
    'max:50',
    Rule::unique('discount_codes', 'code')
        ->ignore($this->id)
        ->whereNull('deleted_at'),
],
            'discount_percentage' => 'required|integer|min:1|max:50',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',

            'usage_type' => 'required|in:once,multiple,limited',
            'max_usage' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->input('usage_type') === 'limited' && (is_null($value) || $value < 1)) {
                        $fail(__('dashboard.The Number of Use field is required and must be greater than zero when choosing the Limited Use type'));
                    }
                }
            ],
            'is_active' => 'required|in:0,1'
        ];
    }


    public function attributes()
    {
        return [
            'code' => __('dashboard.Discount Code'),
            'discount_percentage' => __('dashboard.Discount Percentage'),
            'start_date' => __('dashboard.Start Date'),
            'end_date' => __('dashboard.End Date'),
            'usage_type' => __('dashboard.Number of uses'),
            'max_usage' => __('dashboard.Number of uses'),
            'is_active' => __('dashboard.Stopped'),

        ];
    }
}
