<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

class AddCartRequest extends FormRequest
{
    use ApiTrait;
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
            'note' => 'nullable|string|max:255',

            'delivery_type' => 'nullable|in:delivery,pickup',
            'payment_method' => 'nullable|string|max:50',

            // إما عنوان محفوظ (address_id) أو عنوان لمرة وحدة (address/latitude/longitude)
            // — التحقق الفعلي من وجود واحد منهم عند delivery_type=delivery بدالة withValidator تحت.
            'address_id' => 'nullable|integer|exists:addresses,id',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'coupon_code' => 'nullable|string|max:10',
            'transaction_id' => 'nullable|max:30',

            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable|date_format:H:i:s',
            'cart' => 'required|array|min:1',

            // ملاحظة: ما فيه validation على unit_price لأن السيرفر
            // هو اللي بيحسب السعر دايماً، مش العميل — لحماية من التلاعب بالأسعار.
            'cart.*.product_id' => 'required|integer|exists:products,id',
            'cart.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'cart.*.modifier_value_ids' => 'nullable|array',
            'cart.*.modifier_value_ids.*' => 'integer|exists:attribute_values,id',

            // ✅ استقبال الخصائص العادية (مثل المقاس واللون)
'cart.*.attribute_value_ids' => 'nullable|array',
'cart.*.attribute_value_ids.*' => 'integer|exists:attribute_values,id',

             'cart.*.quantity' => 'required|numeric|min:0.01', // بدل integer|min:1 لدعم الأوزان (بقالة)
            'redeem_points' => 'nullable|integer|min:0',
            'cart.*.slaughter_selected' => 'nullable|boolean',

        ];
    }

    /**
     * لو الطلب "توصيل" ولا وجود address_id، لازم يرسل عنوان كامل
     * (address + latitude + longitude) بدلاً منه.
     */
   public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $deliveryType = $this->input('delivery_type', 'delivery');

        if ($deliveryType === 'delivery' && !$this->input('address_id')) {
            if (!$this->filled('address') || !$this->filled('latitude') || !$this->filled('longitude')) {
                $validator->errors()->add('address', __('api.Address_Required_For_Delivery'));
            }
        }

        // ✅ التاريخ والوقت: إما الاثنين مع بعض، إما ولا واحد فيهم
        $hasDate = $this->filled('delivery_date');
        $hasTime = $this->filled('delivery_time');

        if ($hasDate xor $hasTime) {
            $validator->errors()->add(
                'delivery_date',
                __('api.Delivery_date_and_time_must_be_both_or_none')
            );
        }
    });
}

    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(
            $this->returnError(
                $validator->errors()->first()
            )
        );
    }
    public function attributes()
    {
        return [
            'note' => __('dashboard.Note'),
            'address_id' => __('dashboard.Address'),
            'address' => __('dashboard.Address'),
            'latitude' => __('dashboard.Latitude'),
            'longitude' => __('dashboard.longitude'),
            'delivery_type' => __('dashboard.delivery_type'),
            'payment_method' => __('dashboard.payment_method'),
            'coupon_code' => __('dashboard.Code'),
            'transaction_id' => __('dashboard.Transaction_Number'),
            'cart' => __('dashboard.Cart'),
            'delivery_date' => __('dashboard.Delivery time'),
            'delivery_time' => __('dashboard.Delivery date'),
            'redeem_points'=>__('dashboard.redeem_points'),
        ];
    }
}
