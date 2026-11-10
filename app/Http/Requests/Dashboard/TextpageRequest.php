<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class TextpageRequest extends FormRequest
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
            'name_ar'=>'required|max:150|unique:textpage_translations,name,'.$this->id. ',textpage_id',
            'name_en'=>'required|max:150|unique:textpage_translations,name,'.$this->id. ',textpage_id',
            'details_ar'=>'max:9000',
            'details_en'=>'max:9000',
        ];
    }


    public function attributes()
    {
        return [
            'name_ar'=>__('dashboard.Name'),
            'name_en'=>__('dashboard.Name'),
            'details_ar'=>__('dashboard.Full_Description'),
            'details_en'=>__('dashboard.Full_Description'),
        ];
    }

}
