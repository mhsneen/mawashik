<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GeneralDeliverySettingsRequest extends FormRequest
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
            'longitude' => 'required|numeric',
            'Latitude' => 'required|numeric',
            'delivery_max_distance' => 'required|numeric|min:0',
            'delivery_price_type' => 'required|in:1,2',
            'fixed_delivery_price' => 'nullable|numeric|min:0',
            'dynamic_pricing' => 'nullable|array',
            'dynamic_pricing.*.from' => 'required|numeric|min:0',
            'dynamic_pricing.*.to' => 'required|numeric|min:0|gt:dynamic_pricing.*.from',
            'dynamic_pricing.*.price' => 'required|numeric|min:0',
            'open_pickup'=>'required|in:1,0',
        ];
    }


    public function attributes()
    {
        return [

            'longitude' => __('dashboard.longitude'),
            'Latitude' => __('dashboard.Latitude'),
            'delivery_max_distance' => __('dashboard.delivery_max_distance'),
            'delivery_price' => __('dashboard.Delivery price per kilo'),
            'free_delivery_enabled' => __('dashboard.free_delivery_enabled'),
            'free_delivery_distance' => __('dashboard.free_delivery_distance'),
            'delivery_price_type' => __('dashboard.delivery_price_type'),
            'fixed_delivery_price' => __('dashboard.fixed_delivery_price'),
            'open_pickup' => __('dashboard.allow_pickup_orders'),
        ];
    }
}
