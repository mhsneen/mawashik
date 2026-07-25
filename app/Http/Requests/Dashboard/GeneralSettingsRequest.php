<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
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

            'store_name_ar' => 'required|max:100',
            'store_name_en' => 'required|max:100',
            'store_logo' => 'required|image',
            'store_email' => 'required|email|max:100',
            'store_mobile' => 'required|numeric|digits_between:10,15',
            'store_whatsapp' => 'required|numeric|digits_between:10,15',
            'store_address_ar' => 'required|max:200',
            'store_address_en' => 'required|max:200',


        ];
    }


    public function attributes()
    {
        return [

            'store_name_ar' => __('dashboard.App_Name'),
            'store_name_en' => __('dashboard.App_Name'),
            'store_logo' => __('dashboard.Logo'),
            'store_email' => __('dashboard.Email'),
            'store_whatsapp' => __('dashboard.Whatsapp'),
            'store_address_ar' => __('dashboard.Address'),
            'store_address_en' => __('dashboard.Address'),
            'store_mobile' => __('dashboard.Mobile'),


        ];
    }
}
