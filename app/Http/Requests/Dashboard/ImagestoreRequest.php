<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ImagestoreRequest extends FormRequest
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

            'product_id' => 'required|exists:products,id',
            'multipleimage' => 'required|array|min:1|max:5',
            'multipleimage.*' => 'required|image|max:50',
        ];
    }

    public function attributes()
    {
        return [
            'product_id'=>__('dashboard.product_id'),
            'multipleimage'=>__('dashboard.multipleimage'),
            'multipleimage.*'=>__('dashboard.multipleimage'),
        ];
    }
}
