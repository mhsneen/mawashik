<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\NormalizesSaudiMobile;

class LoginControllerRequest extends FormRequest
{
        use NormalizesSaudiMobile;

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
            'email'=>'required',
            'password'=>'required'
        ];
    }


    public function attributes()
    {
        return [
            'email'=>__('dashboard.Email'),
            'password'=>__('dashboard.Password'),

        ];
    }



}
