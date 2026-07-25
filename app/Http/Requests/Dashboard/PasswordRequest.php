<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'oldpassword'=>'required|current_password',
            'password'=>'required|different:oldpassword',
            'password-confirmation'=>'required|same:password',
        ];
    }


    public function attributes()
    {
        return [
            'oldpassword'=>__('dashboard.Old_Password'),
            'password'=>__('dashboard.New_Password'),
            'password-confirmation'=>__('dashboard.Confirm_Password'),
        ];
    }
}
