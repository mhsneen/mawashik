<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UnitsRequest extends FormRequest
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
 'name_ar' => [
            'required',
            Rule::unique('unit_translations', 'name')
                ->where('locale', 'ar')
                ->ignore(
                    $this->route('unit'),
                    'unit_id'
                ),
        ],

        'name_en' => [
            'required',
            Rule::unique('unit_translations', 'name')
                ->where('locale', 'en')
                ->ignore(
                    $this->route('unit'),
                    'unit_id'
                ),
        ],
        ];
    }


    public function attributes()
    {
        return [
            'name_ar'=>__('dashboard.Name'),
            'name_en'=>__('dashboard.Name'),
        ];
    }
}
