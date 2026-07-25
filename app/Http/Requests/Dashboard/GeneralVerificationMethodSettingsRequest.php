<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GeneralVerificationMethodSettingsRequest extends FormRequest
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

            'verification_method' => 'required|in:sms,whatsapp,email',
 'registration_activation_mode' => 'required|in:direct,verification',
            'sms_provider' => 'required_if:verification_method,sms|string|max:500|nullable',
            'sms_api_key' => 'required_if:verification_method,sms|string|max:500|nullable',
            'sms_sender_id' => 'required_if:verification_method,sms|string|max:500|nullable',
            'sms_username' => 'required_if:verification_method,sms|string|max:500|nullable',
            'sms_password' => 'required_if:verification_method,sms|string|max:500|nullable',
            'sms_base_url' => 'required_if:verification_method,sms|string|max:500|nullable',

              'whatsapp_provider' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_api_url' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_token' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_instance_id' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_phone_number' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_phone_number_id' => 'required_if:verification_method,whatsapp|string|max:500|nullable',
            'whatsapp_business_account_id' => 'required_if:verification_method,whatsapp|string|max:500|nullable',


        'mail_mailer' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_host' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_port' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_username' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_password' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_encryption' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_from_address' => 'required_if:verification_method,email|string|max:500|nullable',
            'mail_from_name' => 'required_if:verification_method,email|string|max:500|nullable',


        ];
    }


    public function attributes()
    {
        return [

            'registration_activation_mode' => __('dashboard.Registration_Activation_Mode'),
            'verification_method' => __('dashboard.verification_method'),
            'sms_provider' => __('dashboard.sms_provider'),
            'sms_api_key' => __('dashboard.sms_api_key'),
            'sms_sender_id' => __('dashboard.sms_sender_id'),
            'sms_username' => __('dashboard.sms_username'),
            'sms_password' => __('dashboard.sms_password'),
            'sms_base_url' => __('dashboard.sms_base_url'),


            'whatsapp_provider' => __('dashboard.whatsapp_provider'),
            'whatsapp_api_url' => __('dashboard.whatsapp_api_url'),
            'whatsapp_token' => __('dashboard.whatsapp_token'),
            'whatsapp_instance_id' => __('dashboard.whatsapp_instance_id'),
            'whatsapp_phone_number' => __('dashboard.whatsapp_phone_number'),
            'whatsapp_phone_number_id' => __('dashboard.whatsapp_phone_number_id'),
            'whatsapp_business_account_id' => __('dashboard.whatsapp_business_account_id'),


             'mail_mailer' => __('dashboard.mail_mailer'),
            'mail_host' => __('dashboard.mail_host'),
            'mail_port' => __('dashboard.mail_port'),
            'mail_username' => __('dashboard.mail_username'),
            'mail_password' => __('dashboard.mail_password'),
            'mail_encryption' => __('dashboard.mail_encryption'),
            'mail_from_address' => __('dashboard.mail_from_address'),
   'mail_from_name' => __('dashboard.mail_from_name'),
        ];
    }
}
