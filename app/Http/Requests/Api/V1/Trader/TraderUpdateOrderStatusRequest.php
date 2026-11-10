<?php

namespace App\Http\Requests\Api\V1\Trader;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;

/**
 * طلب تحديث حالة طلب من قبل التاجر (تأكيد → تجهيز → جاهز).
 * التاجر لا يمكنه إلا التقدّم في الحالات الموثقة للـ state machine:
 *  - 1 → 2 (تأكيد وبدء التجهيز)
 *  - 2 → 3 (تم التجهيز وجاهز للاستلام)
 *  - 1 → 6 (إلغاء من قبل التاجر)
 */
class TraderUpdateOrderStatusRequest extends FormRequest
{
    use ApiTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'    => 'required|integer|exists:orders,id',
            'status_id'   => 'required|integer|in:2,3,6',
            'reason'      => 'nullable|string|max:400',
            'api_password' => 'required|string',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->returnError($validator->errors()->first())
        );
    }
}
