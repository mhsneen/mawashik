<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Dashboard\{GeneralSettingsRequest, GeneralStoreSettingsRequest, GeneralPolicyStoreRequest, GeneralTimesDaysWorkRequest, GeneralDeliverySettingsRequest,GeneralVerificationMethodSettingsRequest,GeneralloyaltypointsSettingsRequest};
use App\Services\ImageUploadService;

class SettingsController extends Controller
{
    protected  $folder = "settings";

    public function __construct()
    {
        $this->middleware('permission:General_Settings')
            ->only(['generalsettings', 'updategeneralsettings']);

        $this->middleware('permission:Store_Settings')
            ->only(['generalstoresettings', 'updatestoregeneralsettings']);


        $this->middleware('permission:Store_policy_settings')
            ->only(['generalpolicystore', 'updategeneralpolicystore']);


        $this->middleware('permission:Working days and times')
            ->only(['generaltimesdayswork', 'updategeneraltimesdayswork']);
        $this->middleware('permission:Delivery_Settings')
            ->only(['generaldeliverysettings', 'updategeneraldeliverysettings']);
    }


    public function generalsettings()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generalsettings', compact('setting'));
    }
    public function updategeneralsettings(GeneralSettingsRequest $request)
    {
        try {
            DB::beginTransaction();
            $store_name = Setting::where('key', 'store_name')->first();
            $store_name_data = [
                'ar' => ['value' => $request->store_name_ar],
                'en' => ['value' => $request->store_name_en]
            ];
            $store_name->update($store_name_data);


            $store_address = Setting::where('key', 'store_address')->first();
            $store_address_data = [
                'ar' => ['value' => $request->store_address_ar],
                'en' => ['value' => $request->store_address_en]
            ];
            $store_address->update($store_address_data);


            Setting::set('store_email', $request->store_email);
            Setting::set('store_mobile', $request->store_mobile);
            Setting::set('store_whatsapp', $request->store_whatsapp);


            if ($request->has('store_logo')) {
                $store_logo_old = Setting::where('key', 'store_logo')->first();
                $imageService = new ImageUploadService();
                $newFilename = $imageService->uploadAndProcess(
                    image: $request->file('store_logo'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $store_logo_old->plain_value // <-- هنا تمرير الصورة القديمة
                );
                Setting::set('store_logo', $newFilename);
            }

            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function generalstoresettings()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generalstoresettings', compact('setting'));
    }


    public function updatestoregeneralsettings(GeneralStoreSettingsRequest $request)
    {
        try {
            DB::beginTransaction();



            $appClosedText = Setting::where('key', 'appClosedText')->first();
            $appClosedText_data = [
                'ar' => ['value' => $request->appClosedText_ar],
                'en' => ['value' => $request->appClosedText_en]
            ];
            $appClosedText->update($appClosedText_data);


            Setting::set('added_tax', $request->added_tax);
            Setting::set('minimum_price_order', $request->minimum_price_order);
            Setting::set('appClosed', $request->appClosed);
            Setting::set('showFullScreenAdvert', $request->showFullScreenAdvert);
            Setting::set('currentVersionNumber', $request->currentVersionNumber);
            Setting::set('appStorePageLink', $request->appStorePageLink);
            Setting::set('googlePlayPageLink', $request->googlePlayPageLink);
            Setting::set('expired_quantities', $request->expired_quantities);

            if ($request->has('bannerLink')) {
                $bannerLink_old = Setting::where('key', 'bannerLink')->first();
                $imageService = new ImageUploadService();
                $newFilename = $imageService->uploadAndProcess(
                    image: $request->file('bannerLink'),
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800,
                    oldFilename: $bannerLink_old->plain_value // <-- هنا تمرير الصورة القديمة
                );
                Setting::set('bannerLink', $newFilename);
            }


            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function generalpolicystore()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generalpolicystore', compact('setting'));
    }


    public function updategeneralpolicystore(GeneralPolicyStoreRequest $request)
    {
        try {
            DB::beginTransaction();



            $free_shipping_sentence = Setting::where('key', 'free_shipping_sentence')->first();
            $free_shipping_sentence_data = [
                'ar' => ['value' => $request->free_shipping_sentence_ar],
                'en' => ['value' => $request->free_shipping_sentence_en]
            ];
            $free_shipping_sentence->update($free_shipping_sentence_data);


            $free_shipping_details = Setting::where('key', 'free_shipping_details')->first();
            $free_shipping_details_data = [
                'ar' => ['value' => $request->free_shipping_details_ar],
                'en' => ['value' => $request->free_shipping_details_en]
            ];
            $free_shipping_details->update($free_shipping_details_data);




            $return_policy_sentence = Setting::where('key', 'return_policy_sentence')->first();
            $return_policy_sentence_data = [
                'ar' => ['value' => $request->return_policy_sentence_ar],
                'en' => ['value' => $request->return_policy_sentence_en]
            ];
            $return_policy_sentence->update($return_policy_sentence_data);


            $return_policy_details = Setting::where('key', 'return_policy_details')->first();
            $return_policy_details_data = [
                'ar' => ['value' => $request->return_policy_details_ar],
                'en' => ['value' => $request->return_policy_details_en]
            ];
            $return_policy_details->update($return_policy_details_data);




            $secure_payment_sentence = Setting::where('key', 'secure_payment_sentence')->first();
            $secure_payment_sentence_data = [
                'ar' => ['value' => $request->secure_payment_sentence_ar],
                'en' => ['value' => $request->secure_payment_sentence_en]
            ];
            $secure_payment_sentence->update($secure_payment_sentence_data);


            $secure_payment_details = Setting::where('key', 'secure_payment_details')->first();
            $secure_payment_details_data = [
                'ar' => ['value' => $request->secure_payment_details_ar],
                'en' => ['value' => $request->secure_payment_details_en]
            ];
            $secure_payment_details->update($secure_payment_details_data);

            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


   public function generaltimesdayswork()
{
    $setting = Setting::get()->keyBy('key');
    $days = [
        'saturday' => 'السبت',
        'sunday' => 'الأحد',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
    ];

    $selectedDays = $setting['days_work']->plain_value ?? [];
    $startWork = $setting['start_work']->plain_value ?? [];
    $endWork = $setting['end_work']->plain_value ?? [];

    if (is_string($selectedDays)) {
        $selectedDays = json_decode($selectedDays, true) ?? [];
    }
    if (is_string($startWork)) {
        $startWork = json_decode($startWork, true) ?? [];
    }
    if (is_string($endWork)) {
        $endWork = json_decode($endWork, true) ?? [];
    }

    return view('dashboard.settings.generaltimesdayswork', compact('setting', 'days', 'selectedDays', 'startWork', 'endWork'));
}

public function updategeneraltimesdayswork(GeneralTimesDaysWorkRequest $request)
{
    try {
        DB::beginTransaction();

        // حفظ البيانات كـ JSON لأنها الآن مصفوفات لكل يوم
        Setting::set('open_work', $request->open_work);
        Setting::set('start_work', $request->start_work ?? []);
        Setting::set('end_work', $request->end_work ?? []);
        Setting::set('days_work', $request->days_work ?? []);

        $out_of_working_hours_message = Setting::where('key', 'out_of_working_hours_message')->first();
        $out_of_working_hours_message_data = [
            'ar' => ['value' => $request->out_of_working_hours_message_ar],
            'en' => ['value' => $request->out_of_working_hours_message_en]
        ];
        $out_of_working_hours_message->update($out_of_working_hours_message_data);

        DB::commit();
        return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
    } catch (Exception $ex) {
        DB::rollback();
        return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
    }
}


    public function generaldeliverysettings()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generaldeliverysettings', compact('setting'));
    }


    public function updategeneraldeliverysettings(GeneralDeliverySettingsRequest $request)
    {

        try {
            DB::beginTransaction();

            Setting::set('longitude', $request->longitude);
            Setting::set('Latitude', $request->Latitude);
            Setting::set('delivery_max_distance', $request->delivery_max_distance);
            Setting::set('delivery_price_type', $request->delivery_price_type);
            Setting::set('fixed_delivery_price', $request->fixed_delivery_price ?? 0);

            if ($request->has('dynamic_pricing')) {
                Setting::set('dynamic_delivery_pricing', json_encode(array_values($request->dynamic_pricing)));
            } else {
                Setting::set('dynamic_delivery_pricing', json_encode([]));
            }


            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


     public function generalverificationmethodsettings()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generalverificationmethodsettings', compact('setting'));
    }

     public function updategeneralverificationmethodsettings(GeneralVerificationMethodSettingsRequest $request)
    {

        try {
            DB::beginTransaction();

          Setting::set('registration_activation_mode', $request->registration_activation_mode);
          Setting::set('verification_method', $request->verification_method);
Setting::set('sms_provider', $request->sms_provider);
Setting::set('sms_api_key', $request->sms_api_key);
Setting::set('sms_sender_id', $request->sms_sender_id);
Setting::set('sms_username', $request->sms_username);
Setting::set('sms_password', $request->sms_password);
Setting::set('sms_base_url', $request->sms_base_url);


Setting::set('whatsapp_provider', $request->whatsapp_provider);
Setting::set('whatsapp_api_url', $request->whatsapp_api_url);
Setting::set('whatsapp_token', $request->whatsapp_token);
Setting::set('whatsapp_instance_id', $request->whatsapp_instance_id);
Setting::set('whatsapp_phone_number', $request->whatsapp_phone_number);
Setting::set('whatsapp_phone_number_id', $request->whatsapp_phone_number_id);
Setting::set('whatsapp_business_account_id', $request->whatsapp_business_account_id);


Setting::set('mail_mailer', $request->mail_mailer);
Setting::set('mail_host', $request->mail_host);
Setting::set('mail_port', $request->mail_port);
Setting::set('mail_username', $request->mail_username);
Setting::set('mail_password', $request->mail_password);
Setting::set('mail_encryption', $request->mail_encryption);
Setting::set('mail_from_address', $request->mail_from_address);
Setting::set('mail_from_name', $request->mail_from_name);


            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function generalloyaltypointssettings()
    {
        $setting = Setting::get()->keyBy('key');

        return view('dashboard.settings.generalloyaltypointssettings', compact('setting'));
    }

     public function updateloyaltypointssettings(GeneralloyaltypointsSettingsRequest $request)
    {

        try {
            DB::beginTransaction();

          Setting::set('loyalty_points_enabled', $request->loyalty_points_enabled);
          Setting::set('loyalty_points_earn_rate', $request->loyalty_points_earn_rate);
Setting::set('loyalty_points_redeem_value', $request->loyalty_points_redeem_value);
Setting::set('loyalty_points_min_redeem', $request->loyalty_points_min_redeem);
            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }
}
