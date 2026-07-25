<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductsSearchRequest extends FormRequest
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
            'search_diffrnetPrice'=>'required_with:search_netPrice',
            'search_diffrquantity'=>'required_with:search_quantity',
            'search_netPrice'=>'nullable|numeric|required_with:search_diffrnetPrice|gt:0',
            'search_quantity'=>'nullable|numeric|required_with:search_diffrquantity|gt:0',

        ];
    }


    public function attributes()
    {
        return [
            'search_diffrnetPrice'=>__('dashboard.Standard'),
            'search_diffrquantity'=>__('dashboard.Standard'),
            'search_netPrice'=>__('dashboard.Price'),
            'search_quantity'=>__('dashboard.Quantity'),

        ];
    }
}
