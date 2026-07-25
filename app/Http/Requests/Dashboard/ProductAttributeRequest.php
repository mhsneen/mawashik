<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeRequest extends FormRequest
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
        $rules = [];

        if ($this->has('attribute_values')) {
            foreach ($this->attribute_values as $attributeId => $values) {
                $rules["attribute_values.$attributeId"] = 'array|min:1';
                $rules["attribute_values.$attributeId.*"] = 'exists:attribute_values,id';
            }
        }

        return $rules;
    }


    public function attributes()
    {
        return [
            'attribute_values' => __('dashboard.Product_Features'),

        ];
    }
}
