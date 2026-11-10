<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name' => 'required|max:150',
            'is_active' => 'required|in:0,1',
            'image' => 'nullable|image|max:8192',
            'email' => 'required|string|max:255|unique:users,email,' . $this->id . ',id',
            'mobile' => 'required|string|max:255|unique:users,mobile,' . $this->id . ',id',
            'password' => 'required_without:id|string|min:6',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }


    public function attributes()
    {
        return [
            'name' => __('dashboard.Name'),
            'email' => __('dashboard.Email'),
            'image' => __('dashboard.Image'),
            'is_active' => __('dashboard.Efficiency_Status'),
            'mobile' => __('dashboard.Mobile'),
            'password' => __('dashboard.Password'),
            'permissions' => __('dashboard.Permission_validation'),
            'permissions.0' => __('dashboard.Permission_validation'),
        ];
    }
}
