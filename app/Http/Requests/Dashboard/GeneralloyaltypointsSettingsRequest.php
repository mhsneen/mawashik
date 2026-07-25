<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneralloyaltypointsSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
       return [
            'loyalty_points_enabled' => [
                'required',
                'boolean',
            ],

            'loyalty_points_earn_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'loyalty_points_redeem_value' => [
                'required',
                'numeric',
                'min:0',
            ],

            'loyalty_points_min_redeem' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    public function attributes()
    {
        return [


             'loyalty_points_enabled.required' => __('dashboard.Loyalty_points_enabled_required'),
            'loyalty_points_enabled.boolean' => __('dashboard.Loyalty_points_enabled_boolean'),

            'loyalty_points_earn_rate.required' => __('dashboard.Loyalty_points_earn_rate_required'),
            'loyalty_points_earn_rate.numeric' => __('dashboard.Loyalty_points_earn_rate_numeric'),

            'loyalty_points_redeem_value.required' => __('dashboard.Loyalty_points_redeem_value_required'),
            'loyalty_points_redeem_value.numeric' => __('dashboard.Loyalty_points_redeem_value_numeric'),

            'loyalty_points_min_redeem.required' => __('dashboard.Loyalty_points_min_redeem_required'),
            'loyalty_points_min_redeem.integer' => __('dashboard.Loyalty_points_min_redeem_integer'),
        ];
    }
}
