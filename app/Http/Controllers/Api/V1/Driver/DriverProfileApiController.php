<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\{Wallet, WalletTransaction};
use App\Traits\ApiTrait;
use App\Services\ImageUploadService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverProfileApiController extends Controller
{
    use ApiTrait;

    protected $folder = 'users';

    /**
     * بيانات السائق الحالي مع إحصائيات الطلبيات والمحفظة
     */
    public function get_DriverInfo(Request $request)
    {
        try {
            $driver = $request->user();

            $completedCount  = $driver->driverOrders()->where('status_id', 5)->count();
            $inDeliveryCount = $driver->driverOrders()->where('status_id', 4)->count();
            $pendingCount    = $driver->driverOrders()->where('status_id', 3)->count();
            $wallet          = $driver->wallet;

            return $this->returnData('data', [
                'id'              => $driver->id,
                'name'            => $driver->name,
                'mobile'          => $driver->mobile,
                'email' => $driver->email ?? '',
                'image_url'       => $driver->image_url,
                'base_image_url'  => $driver->base_image_url,
                'is_active'       => (bool) $driver->is_active,
                'is_available'    => (bool) $driver->is_available,    // ✅ جديد: toggle استقبال الطلبات
                'role'            => 'driver',
                'stats' => [
                    'completed_orders'  => $completedCount,
                    'in_delivery'       => $inDeliveryCount,
                    'pending_orders'    => $pendingCount,
                ],
                'wallet' => [
                    'balance'   => $wallet?->balance ?? 0,
                ],
            ], __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('get_DriverInfo: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تحديث بيانات السائق (بما في ذلك صورة الهوية والرخصة).
     */
    public function driver_Update(Request $request, ImageUploadService $imageService)
    {
        $driver = $request->user();

        $validator = Validator::make($request->all(), [
            'name'             => 'sometimes|required|max:150',
            'mobile' => [
                'sometimes', 'required', 'numeric', 'digits_between:8,20',
                Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore($driver->id),
            ],
            'email' => [
                'sometimes', 'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($driver->id),
            ],
            'image'            => 'sometimes|nullable|image|max:5120',
            'license_image'    => 'sometimes|nullable|image|max:5120',
            'device_token'     => 'sometimes|nullable|string|max:255',
            'city_id'          => 'sometimes|nullable|exists:cities,id',
            'area_id'          => 'sometimes|nullable|exists:areas,id',
            'neighborhood_id'  => 'sometimes|nullable|exists:neighborhoods,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $data = [];
            foreach (['name', 'mobile', 'email', 'device_token', 'city_id', 'area_id', 'neighborhood_id'] as $field) {
                if ($request->filled($field)) {
                    $data[$field] = $request->$field;
                }
            }

            if ($request->hasFile('image')) {
                $data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 300,
                    thumbnailHeight: 300,
                    oldFilename: $driver->image
                );
            }

            if ($request->hasFile('license_image')) {
                $data['license_image'] = $imageService->uploadAndProcess(
                    image: $request->file('license_image'),
                    folder: $this->folder,
                    thumbnailWidth: 400,
                    thumbnailHeight: 300,
                    oldFilename: $driver->license_image
                );
            }

            $driver->update($data);
            DB::commit();

            return $this->returnSuccessMessage(__('api.Profile_Updated_Success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('driver_Update: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * رصيد محفظة السائق + ملخص كامل
     */
       /**
     * رصيد محفظة السائق + ملخص كامل
     */
    public function getWalletBalance(Request $request)
    {
        try {
            $driver = $request->user();

            // 1. رصيد المحفظة (الصافي المتبقي للسائق أو المديونية للمتجر)
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $driver->id],
                ['balance' => 0, 'rolename' => 'driver']
            );

            // استعلام أساسي لجميع الطلبات المكتملة للسائق
            $completedOrdersQuery = $driver->driverOrders()->where('status_id', 5);

            // 2. إحصائيات الطلبات
            $todayOrders = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->count();
            $totalOrders = $completedOrdersQuery->count();

            // 3. الكاش المستلم (الطلبات الكاش: السائق استلم المبلغ الكامل final_price من العميل)
            $cashCollected = (clone $completedOrdersQuery)->where('payment_method', 'cash')->sum('final_price');

            // 4. المستحقات الإلكترونية (أجر التوصيل delivery_price للطلبات المدفوعة أونلاين أو بالمحفظة)
            // المتجر استلم المبلغ كاملاً، ويجب أن يدفع للسائق أجر التوصيل فقط
            $onlineEarnings = (clone $completedOrdersQuery)->whereIn('payment_method', ['online', 'wallet'])->sum('delivery_price');

            // 5. إجمالي الأرباح (كل أجور التوصيل للطلبات الكاش + الإلكترونية)
            $totalEarnings = (clone $completedOrdersQuery)->sum('delivery_price');

            // ✅ مواشي وسيط: تفصيل العمولات للمندوب
            $totalCommission = (clone $completedOrdersQuery)->sum('commission_driver');
            $todayCommission = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->sum('commission_driver');
            $monthCommission = (clone $completedOrdersQuery)->whereMonth('delivered_at', now()->month)
                ->whereYear('delivered_at', now()->year)->sum('commission_driver');

            // ✅ مواشي وسيط: العربون (للمندوب يحتاج يعرف كم يستلم نقدي)
            $totalCashToCollect = (clone $completedOrdersQuery)->where('payment_method', 'cash')
                ->sum('remaining_amount');

            return $this->returnData('data', [
                'balance'            => (float) $wallet->balance,
                'today_orders'       => $todayOrders,
                'total_orders'       => $totalOrders,

                'cash_collected'     => (float) $cashCollected,    // الكاش الذي استلمه السائق بيده
                'online_earnings'    => (float) $onlineEarnings,    // أجر التوصيل المستحق للسائق من الطلبات الإلكترونية
                'total_earnings'     => (float) $totalEarnings,    // إجمالي كل أرباح السائق من التوصيل

                // ✅ مواشي وسيط: العمولات التفصيلية
                'commission' => [
                    'total_today'  => (float) $todayCommission,
                    'total_month'  => (float) $monthCommission,
                    'total_all'    => (float) $totalCommission,
                    'rate_percent' => $driver->driver_commission_rate !== null
                        ? (float) $driver->driver_commission_rate
                        : null,  // null = يستخدم النسبة الافتراضية من الإعدادات
                ],

                // ✅ مواشي وسيط: الكاش المتبقي على العملاء (للطلبات النقدية)
                'cash_to_collect_total' => (float) $totalCashToCollect,

                // ✅ إضافة متوسط التقييم وعدد التقييمات
                'average_rating'     => (float) $driver->average_rating ?? 0.0,
                'reviews_count'      => (int) $driver->reviews_count ?? 0,
            ], __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('getWalletBalance: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * سجل معاملات محفظة السائق (مُرقّم الصفحات)
     */
        /**
     * سجل معاملات محفظة السائق
     */
       public function getWalletTransactions(Request $request)
    {
        try {
            $driver = $request->user();
            $wallet = Wallet::where('user_id', $driver->id)->first();

            if (!$wallet) {
                return $this->returnData('data', [], __('api.Api_Return_Data'));
            }

            $transactions = WalletTransaction::where('wallet_id', $wallet->id)
                ->with('order:id,final_price,delivery_price,payment_method')
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($tx) {
                    return [
                        'id'            => $tx->id,
                        'type'          => $tx->type,
                        'type_label'    => $tx->type_translated,
                        'amount'        => $tx->amount,
                        'amount_signed' => $tx->amount_signed,
                        'description'   => $tx->description,
                        'order_id'      => $tx->order_id,

                        // ✅ تنسيق التاريخ والوقت ليكون واضحاً ومنظماً (مثال: 2026-08-10 03:52 م)
                        'date'          => $tx->created_at
                            ? \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d h:i A')
                            : null,
                    ];
                })
                ->values();

            return $this->returnData('data', $transactions, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('getWalletTransactions: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تبديل حالة استقبال الطلبات (toggle).
     * - true  → متاح لاستقبال الطلبات.
     * - false → غير متاح (لا يظهر في إسناد الطلبات).
     *
     * يستخدم في شاشة "الرئيسية" للمندوب (toggle "استقبال الطلبات: متاح/غير متاح").
     */
    public function toggleAvailability(Request $request)
    {
        try {
            $driver = $request->user();

            $request->validate([
                'is_available' => 'required|boolean',

            ]);

            $driver->update([
                'is_available' => $request->boolean('is_available'),
            ]);

            return $this->returnData('data', [
                'is_available' => (bool) $driver->fresh()->is_available,
            ], __('api.Availability_Updated_Success'));
        } catch (Exception $ex) {
            Log::error('driver.toggleAvailability: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تحديث موقع المندوب الحي (Live Tracking).
     *
     * يستدعيه تطبيق المندوب كل بضع ثوانٍ أثناء التوصيل.
     * التطبيق يقرأ: POST /api/v1/driver/updateLocation
     *
     * المدخلات: lat, lng, heading (اختياري), speed (اختياري).
     */
    public function updateLocation(Request $request)
    {
        try {
            $driver = $request->user();

            $request->validate([
                'lat'          => 'required|numeric|between:-90,90',
                'lng'          => 'required|numeric|between:-180,180',
                'heading'      => 'nullable|numeric|between:0,360',
                'speed'        => 'nullable|numeric|min:0',
            ]);

            \App\Models\DriverLocation::updateOrCreateFor(
                $driver->id,
                (float) $request->lat,
                (float) $request->lng,
                $request->heading !== null ? (float) $request->heading : null,
                $request->speed !== null ? (float) $request->speed : null,
            );

            return $this->returnSuccessMessage(__('api.Location_Updated_Success'));
        } catch (Exception $ex) {
            Log::error('driver.updateLocation: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
