<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class RestPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',

        ];
    }


    public function attributes()
    {
        return [
            'email'=>__('dashboard.Email'),
            'password'=>__('dashboard.New_Password'),
            'password-confirmation'=>__('dashboard.Confirm_Password'),
        ];
    }
}
