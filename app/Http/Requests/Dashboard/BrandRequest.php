<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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

            'name_ar' => 'required|unique:brand_translations,name,' . $this->id . ',brand_id',
            'name_en' => 'required|unique:brand_translations,name,' . $this->id . ',brand_id',
            'image' => 'required_without:id|image',
            'is_active' => 'required|in:0,1'
        ];
    }


    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.Name'),
            'name_en' => __('dashboard.Name'),
            'image' => __('dashboard.Image'),
            'is_active' => __('dashboard.Efficiency_Status'),
        ];
    }
}
