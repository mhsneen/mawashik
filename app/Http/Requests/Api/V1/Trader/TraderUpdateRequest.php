<?php

namespace App\Http\Requests\Api\V1\Trader;

use App\Traits\ApiTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TraderUpdateRequest extends FormRequest
{
    use ApiTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'sometimes|string|max:150',
            'commercial_register' => 'nullable|string|max:100',
            'store_lat'           => 'sometimes|numeric|between:-90,90',
            'store_lng'           => 'sometimes|numeric|between:-180,180',
            //'store_address'       => 'nullable|string|max:255',
            'city_id'             => 'sometimes|exists:cities,id',
            'area_id'             => 'nullable|exists:areas,id',
            'neighborhood_id'     => 'nullable|exists:neighborhoods,id',
            'whatsapp_number'     => 'nullable|string|max:30',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'banner_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ];
    }

    public function messages(): array
    {
        return [
            'store_lat.between'      => __('api.Invalid_Latitude'),
            'store_lng.between'      => __('api.Invalid_Longitude'),
            'city_id.exists'         => __('api.City_Not_Found'),
            'area_id.exists'         => __('api.Area_Not_Found'),
            'neighborhood_id.exists' => __('api.Neighborhood_Not_Found'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                => __('dashboard.Trader_Name'),
            'commercial_register' => __('dashboard.Commercial_Register'),
            'store_lat'           => __('dashboard.Store_Lat'),
            'store_lng'           => __('dashboard.Store_Lng'),
           // 'store_address'       => __('dashboard.Store_Address'),
            'city_id'             => __('dashboard.City'),
            'area_id'             => __('dashboard.Area'),
            'neighborhood_id'     => __('dashboard.Neighborhood'),
            'image'               => __('dashboard.Image'),
            'banner_image'        => __('dashboard.banner_image'),
            'whatsapp_number'     => __('dashboard.Whatsapp'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}