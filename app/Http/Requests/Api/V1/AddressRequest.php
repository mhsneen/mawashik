<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class AddressRequest extends FormRequest
{
    use ApiTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'building_number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'apartment_number' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
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
            'title' => __('dashboard.Address_Title'),
            'address' => __('dashboard.Address'),
            'latitude' => __('dashboard.Latitude'),
            'longitude' => __('dashboard.longitude'),
        ];
    }
}
