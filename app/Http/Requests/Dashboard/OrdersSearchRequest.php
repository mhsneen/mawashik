<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OrdersSearchRequest extends FormRequest
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
            'search_order_number'=>'nullable|numeric|exists:orders,id',
            'search_client_name'=>'nullable|string|max:25',
            'search_client_mobile'=>'nullable|string|max:25',
            'search_status_id'=>'nullable|numeric|exists:orderstatuses,id',
            'search_payment_status'=>'nullable|numeric|exists:paymentstatuses,id',
            'search_date_from'=>'required_with:search_date_to|nullable|date|date_format:Y-m-d',
            'search_date_to'=>'required_with:search_date_from|nullable|date|date_format:Y-m-d',
        ];
    }


    public function attributes()
    {
        return [
            'search_order_number'=>__('dashboard.Order_Number'),
            'search_client_name'=>__('dashboard.Client_Name'),
            'search_client_mobile'=>__('dashboard.Client_Mobile'),
            'search_status_id'=>__('dashboard.Order_Status'),
            'search_payment_status'=>__('dashboard.Payment_Statussingle'),
            'search_date_from'=>__('dashboard.Date_From'),
            'search_date_to'=>__('dashboard.Date_To'),
        ];
    }
}
