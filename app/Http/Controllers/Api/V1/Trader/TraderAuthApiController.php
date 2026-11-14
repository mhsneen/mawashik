<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Trader\TraderRegisterRequest;
use App\Models\{User, OtpCode};
use App\Services\{OtpService, FCMServices};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * TraderAuthApiController — مصادقة التاجر.
 *
 * العمليات:
 *  - register: تسجيل تاجر جديد (حساب pending بانتظار اعتماد الأدمن).
 *  - login: تسجيل دخول عبر OTP (يُعاد استخدام AuthCommonApiController).
 *  - check_active_code: تفعيل الحساب عبر OTP.
 *  - get_TraderInfo: معلومات التاجر بعد الاعتماد.
 *  - update: تحديث بيانات التاجر.
 *  - logout: تسجيل خروج.
 *
 * ملاحظة: نفس OTP service يُعاد استخدامه (مع نوع registration_trader).
 */
class TraderAuthApiController extends Controller
{
    use ApiTrait;

    public function register(TraderRegisterRequest $request)
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            // إنشاء المستخدم
            $trader = User::create([
                'name'                => $data['name'],
                'mobile'              => $data['mobile'],
                'password'            => $data['password'],
                'id_number'           => $data['id_number'],
                'commercial_register' => $data['commercial_register'] ?? null,
                'store_lat'           => $data['store_lat'],
                'store_lng'           => $data['store_lng'],
                'store_address'       => $data['store_address'],
                'city_id'             => $data['city_id'],
                'area_id'             => $data['area_id'] ?? null,
                'neighborhood_id'     => $data['neighborhood_id'] ?? null,
                'trader_status'       => 'pending',
                'is_active'           => 0,  // يُفعّل عند الاعتماد
            ]);

            // تعيين دور trader
            $trader->assignRole('trader');

            // توليد OTP وإرساله
            $otpService = app(OtpService::class);
            $otpService->generateAndSend($trader, 'registration');

            DB::commit();

            return $this->returnData('data', [
                'user_id' => $trader->id,
                'trader_status' => 'pending',
                'requires_activation' => true,
            ], __('api.Registration_Code_Sent_Verify'));

        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Trader register: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * معلومات التاجر الحالي.
     */
    public function get_TraderInfo(Request $request)
    {
        try {
            $trader = $request->user();

            return $this->returnData('data', [
                'id'                  => $trader->id,
                'name'                => $trader->name,
                'mobile'              => $trader->mobile,
                'whatsapp_number'     => $trader->whatsapp_e164,    // ✅ جديد
                'whatsapp_link'       => $trader->whatsapp_link,    // ✅ جديد
                'image'               => $trader->image_url,
                'banner'              => $trader->banner_url,        // ✅ جديد
                'trader_status'       => $trader->trader_status,
                'id_number'           => $trader->id_number,
                'commercial_register' => $trader->commercial_register,
                'store_address'       => $trader->store_address,
                'store_lat'           => $trader->store_lat,
                'store_lng'           => $trader->store_lng,
                'commission_rate'     => $trader->commission_rate,
                'city'                => $trader->city?->translate(app()->getLocale())?->name,
                'area'                => $trader->area?->translate(app()->getLocale())?->name,
                'neighborhood'       => $trader->neighborhood?->translate(app()->getLocale())?->name,
            ], __('api.Api_Return_Data'));

        } catch (\Exception $ex) {
            Log::error('get_TraderInfo: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تحديث بيانات التاجر (الاسم، العنوان، الموقع، رقم السجل، الواتساب).
     */
    public function update(Request $request)
    {
        try {
            $trader = $request->user();

            $request->validate([
                'name'                => 'sometimes|string|max:150',
                'commercial_register' => 'nullable|string|max:100',
                'store_lat'           => 'sometimes|numeric|between:-90,90',
                'store_lng'           => 'sometimes|numeric|between:-180,180',
                'store_address'       => 'sometimes|string|max:255',
                'city_id'             => 'sometimes|exists:cities,id',
                'area_id'             => 'nullable|exists:areas,id',
                'neighborhood_id'     => 'nullable|exists:neighborhoods,id',
                'image'               => 'nullable|string',
                'whatsapp_number'     => 'sometimes|nullable|string|max:30', // ✅ جديد
            ]);

            $trader->update($request->only([
                'name', 'commercial_register', 'store_lat', 'store_lng',
                'store_address', 'city_id', 'area_id', 'neighborhood_id', 'image',
                'whatsapp_number',
            ]));

            return $this->returnData('data', [
                'id' => $trader->id,
            ], __('api.Api_Update_Success'));
        } catch (\Exception $ex) {
            Log::error('Trader update: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
