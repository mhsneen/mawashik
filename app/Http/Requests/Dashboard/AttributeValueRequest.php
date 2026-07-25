<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
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

            'value_ar' => 'required|unique:attribute_value_translations,value,' . $this->id . ',attribute_value_id',
            'value_en' => 'required|unique:attribute_value_translations,value,' . $this->id . ',attribute_value_id',
            'color_code' => 'nullable'
        ];
    }


    public function attributes()
    {
        return [
            'value_ar' => __('dashboard.Property values'),
            'value_en' => __('dashboard.Property values'),

        ];
    }
}
