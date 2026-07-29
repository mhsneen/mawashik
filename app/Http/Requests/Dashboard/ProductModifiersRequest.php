<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductModifiersRequest extends FormRequest
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
            'modifiers' => 'required|array|min:1',
            'modifiers.*.price' => 'nullable|numeric|min:0',
            'modifiers.*.quantity' => 'nullable|numeric|min:0',
            'modifiers.*.is_default' => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'modifiers.*.price' => __('dashboard.Price'),
            'modifiers.*.quantity' => __('dashboard.Quantity'),
        ];
    }
}
