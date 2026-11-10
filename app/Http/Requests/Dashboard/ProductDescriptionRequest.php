<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductDescriptionRequest extends FormRequest
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
            'id' => 'required|exists:products,id',
            'description_ar' => 'nullable',
            'description_en' => 'nullable',
            'details_ar' => 'nullable',
            'details_en' => 'nullable',


        ];
    }


    public function attributes()
    {
        return [
            'description_ar' => __('dashboard.Short_Description'),
            'description_en' => __('dashboard.Short_Description'),
            'details_ar' => __('dashboard.Full_Description'),
            'details_en' => __('dashboard.Full_Description'),
        ];
    }
}
