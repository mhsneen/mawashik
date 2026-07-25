<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
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
            'name'      => 'required|max:150',
            'is_active' => 'required|in:0,1',
            'image'     => 'nullable|image',
            'mobile'    => [
                'required',
                'numeric',
                'digits_between:10,20',
                Rule::unique('users', 'mobile')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($this->id),
            ],
        ];
    }


    public function attributes()
    {
        return [
            'name' => __('dashboard.Name'),
            'image' => __('dashboard.Image'),
            'is_active' => __('dashboard.Efficiency_Status'),
            'mobile' => __('dashboard.Mobile'),
            'email' => __('dashboard.Email'),
        ];
    }
}
