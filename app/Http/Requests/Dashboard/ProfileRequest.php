<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'mobile' => 'required|numeric|unique:users,mobile,' . $this->id,
            'image' => 'required_without:id|image',
        ];
    }


    public function attributes()
    {
        return [
            'name' => __('dashboard.Name'),
            'email' => __('dashboard.Email'),
            'mobile' => __('dashboard.Mobile'),
            'image' => __('dashboard.Image'),
        ];
    }
}
