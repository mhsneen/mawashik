<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GeneralPolicyStoreRequest extends FormRequest
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


            'free_shipping_sentence_ar' => 'nullable|max:100',
            'free_shipping_sentence_en' => 'nullable|max:100',
            'free_shipping_details_ar' => 'nullable|max:800',
            'free_shipping_details_en' => 'nullable|max:800',


            'return_policy_sentence_ar' => 'nullable|max:100',
            'return_policy_sentence_en' => 'nullable|max:100',
            'return_policy_details_ar' => 'nullable|max:800',
            'return_policy_details_en' => 'nullable|max:800',


            'secure_payment_sentence_ar' => 'nullable|max:100',
            'secure_payment_sentence_en' => 'nullable|max:100',
            'secure_payment_details_ar' => 'nullable|max:800',
            'secure_payment_details_en' => 'nullable|max:800',


        ];
    }


    public function attributes()
    {
        return [

            'free_shipping_sentence_ar' =>
            __('dashboard.Free shipping') . ' ' . __('dashboard.sentence'),
            'free_shipping_sentence_en' =>
            __('dashboard.Free shipping') . ' ' . __('dashboard.sentence'),

            'free_shipping_details_ar' =>
            __('dashboard.Free shipping') . ' ' . __('dashboard.details'),
            'free_shipping_details_en' =>
            __('dashboard.Free shipping') . ' ' . __('dashboard.details'),

            'return_policy_sentence_ar' =>
            __('dashboard.Return policy') . ' ' . __('dashboard.sentence'),
            'return_policy_sentence_en' =>
            __('dashboard.Return policy') . ' ' . __('dashboard.sentence'),

            'return_policy_details_ar' =>
            __('dashboard.Return policy') . ' ' . __('dashboard.details'),
            'return_policy_details_en' =>
            __('dashboard.Return policy') . ' ' . __('dashboard.details'),



            'secure_payment_sentence_ar' =>
            __('dashboard.Secure payment') . ' ' . __('dashboard.sentence'),
            'secure_payment_sentence_en' =>
            __('dashboard.Secure payment') . ' ' . __('dashboard.sentence'),

            'secure_payment_details_ar' =>
            __('dashboard.Secure payment') . ' ' . __('dashboard.details'),
            'secure_payment_details_en' =>
            __('dashboard.Secure payment') . ' ' . __('dashboard.details'),

        ];
    }
}
