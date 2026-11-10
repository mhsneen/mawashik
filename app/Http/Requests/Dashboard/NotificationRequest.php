<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'recivers' => 'required',
            'msg_details' => 'required|max:50',
            'msg_title' => 'required|max:20',
        ];
    }


    public function attributes()
    {
        return [
            'msg_details' => __('dashboard.Message_text'),
            'msg_title' => __('dashboard.Title'),
            'recivers' => __('dashboard.Receiver'),
        ];
    }
}
