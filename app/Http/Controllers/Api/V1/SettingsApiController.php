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


    public function getsettings_showFullScreenAdvert()
    {
        try {
            DB::beginTransaction();


             $settings = Setting::whereIn(
            'key',
            ['showFullScreenAdvert', 'bannerLink']
        )
        ->get()
        ->keyBy('key');

        $data = [
            'showFullScreenAdvert' => $settings['showFullScreenAdvert']->plain_value ?? false,
            'bannerLink' => $settings['bannerLink']->final_value ?? "",
        ];

            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('getsettings_showFullScreenAdvert: ' . $ex->getMessage(), [
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


    public function getsettings()
    {
        try {
            DB::beginTransaction();


             $settings = Setting::whereIn(
            'key',
            ['store_logo','showFullScreenAdvert', 'bannerLink',
            'store_email','store_mobile','store_whatsapp','added_tax','currentVersionNumber',
            'appStorePageLink',
            'googlePlayPageLink',
            'store_name',
            'store_address',
            'free_shipping_sentence',
            'free_shipping_details',
            'return_policy_sentence',
            'return_policy_details',
            'secure_payment_sentence',
            'secure_payment_details',
            'minimum_price_order',

            ]
        )
        ->get()
        ->keyBy('key');

        $data = [
            'showFullScreenAdvert' => $settings['showFullScreenAdvert']->plain_value ?? false,
            'bannerLink' => $settings['bannerLink']->final_value ?? "",
            'store_logo' => $settings['store_logo']->final_value ?? "",
            'store_email' => $settings['store_email']->final_value ?? "",
            'store_mobile' => $settings['store_mobile']->final_value ?? "",
            'store_whatsapp' => $settings['store_whatsapp']->final_value ?? "",
            'added_tax' => $settings['added_tax']->final_value ?? "",
            'currentVersionNumber' => $settings['currentVersionNumber']->final_value ?? "",
            'appStorePageLink' => $settings['appStorePageLink']->final_value ?? "",
            'googlePlayPageLink' => $settings['googlePlayPageLink']->final_value ?? "",
            'store_name' => $settings['store_name']->final_value ?? "",
            'store_address' => $settings['store_address']->final_value ?? "",
            'free_shipping_sentence' => $settings['free_shipping_sentence']->final_value ?? "",
            'free_shipping_details' => $settings['free_shipping_details']->final_value ?? "",
            'return_policy_sentence' => $settings['return_policy_sentence']->final_value ?? "",
            'return_policy_details' => $settings['return_policy_details']->final_value ?? "",
            'secure_payment_sentence' => $settings['secure_payment_sentence']->final_value ?? "",
            'secure_payment_details' => $settings['secure_payment_details']->final_value ?? "",
            'minimum_price_order' => $settings['minimum_price_order']->final_value ?? "",
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
}
