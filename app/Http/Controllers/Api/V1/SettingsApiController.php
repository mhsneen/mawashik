<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StoreStatusService;
class SettingsApiController extends Controller
{
    use ApiTrait;




    public function getsettings_added_tax()
    {
        try {
            DB::beginTransaction();


            $pricing = Setting::wherein(
                'key',
                ['added_tax']
            )->select(array('key', 'plain_value'))
                ->pluck('plain_value', 'key');
            $data = [
                'added_tax' =>  $pricing['added_tax']
            ];
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('getsettings_added_tax: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }



    public function getsettings_whattapp()
    {
        try {
            DB::beginTransaction();
            $whatsapp = Setting::where('key', 'store_whatsapp')->value('plain_value');

            // تحويل الرقم إلى نص وإزالة أي + أو 0 في البداية
            $whatsapp = ltrim(str_replace('+', '', (string)$whatsapp), '0');

            // دمج مقدمة الدولة
            $numberWithCountryCode = "966" . $whatsapp;

            $data = [
                'whatsapp' => $numberWithCountryCode
            ];
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }


    public function getsettings()
    {
        try {
            DB::beginTransaction();


            $settings = Setting::all()
                ->keyBy('key')
                ->map(fn($item) => $item->final_value);

            $data = [
                'setting' => $settings
            ];

            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('getsettings: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * حالة المتجر الكاملة: صيانة + مفتوح الآن ولا لا + جدول أيام العمل —
     * تُستخدم عند فتح التطبيق لعرض بانر/شاشة "مغلق حالياً" وتعطيل الطلب
     * بدون ما يحتاج المستخدم يوصل لشاشة السلة عشان يكتشف ذلك.
     */
    public function getStoreStatus(StoreStatusService $storeStatus)
    {
        try {
            return $this->returnData('data', $storeStatus->getStatus(), __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('getStoreStatus: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
