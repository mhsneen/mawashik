<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GeneralStoreSettingsRequest extends FormRequest
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

            'added_tax' => 'required|numeric',
          //  'appClosed' => 'required|in:0,1',
           // 'appClosedText_ar' => 'nullable|max:800',
            //'appClosedText_en' => 'nullable|max:800',
            //'showFullScreenAdvert' => 'required|in:0,1',
            'currentVersionNumber' => 'nullable|max:100',
            'appStorePageLink' => 'nullable|url',
            'googlePlayPageLink' => 'nullable|url',
         //   'bannerLink' => 'nullable|image',
            'expired_quantities' => 'required|numeric|min:0',
           // 'minimum_price_order' => 'nullable|numeric',

        ];
    }


    public function attributes()
    {
        return [

            'added_tax' => __('dashboard.added_tax'),
            'appClosed' => __('dashboard.The application is undergoing maintenance'),
            'appClosedText_ar' => __('dashboard.Maintenance text'),
            'appClosedText_en' => __('dashboard.Maintenance text'),
            'showFullScreenAdvert' => __('dashboard.Fullscreen advertisement'),
            'currentVersionNumber' => __('dashboard.currentVersionNumber'),
            'appStorePageLink' => __('dashboard.AppStore'),
            'googlePlayPageLink' => __('dashboard.Googleaplay'),
            'bannerLink' => __('dashboard.Adimage'),
            'expired_quantities' => __('dashboard.expired_quantities'),
            'minimum_price_order' => __('dashboard.minimum_price_order'),


        ];
    }
}
