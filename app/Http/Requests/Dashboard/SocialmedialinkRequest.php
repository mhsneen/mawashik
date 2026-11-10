<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SocialmedialinkRequest extends FormRequest
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
            'name' => 'required|max:150|unique:socialmedialinks,name,' . $this->id . ',id',
            'icon' => 'required_without:id|image',
            'link' => 'required|active_url|max:100',

        ];
    }


    public function attributes()
    {
        return [
            'name' => __('dashboard.Name'),
            'icon' => __('dashboard.Logo'),
            'link' => __('dashboard.Link'),
        ];
    }
}
