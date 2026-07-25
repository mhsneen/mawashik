<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
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
            'name_ar' => 'required|max:100',
            'name_en' => 'required|max:100',
            'details_ar' => 'required|max:400',
            'details_en' => 'required|max:400',
            'image' => 'required_without:id|image',

        ];
    }


    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.Name'),
            'name_en' => __('dashboard.Name'),
            'details_ar' => __('dashboard.Description'),
            'details_en' => __('dashboard.Description'),
            'image' => __('dashboard.Image'),

        ];
    }
}
