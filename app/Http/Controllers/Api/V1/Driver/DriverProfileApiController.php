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
                'is_active'       => $driver->is_active,
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
     * تحديث بيانات السائق (نفس منطق client_Update)
     */
    public function driver_Update(Request $request, ImageUploadService $imageService)
    {
        $driver = $request->user();

        $validator = Validator::make($request->all(), [
            'name'   => 'required|max:100',
            'mobile' => [
                'required', 'numeric', 'digits_between:10,20',
                Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore($driver->id),
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($driver->id),
            ],
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $data = [
                'name'   => $request->name,
                'mobile' => $request->mobile,
                'email'  => $request->email,
            ];

            if ($request->hasFile('image')) {
                $data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $driver->image
                );
            }

            $driver->update($data);
            DB::commit();

            return $this->returnSuccessMessage(__('api.Api_Data_has_been_modified_successfully'));
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

            return $this->returnData('data', [
                'balance'            => (float) $wallet->balance,
                'today_orders'       => $todayOrders,
                'total_orders'       => $totalOrders,
                
                'cash_collected'     => (float) $cashCollected,    // الكاش الذي استلمه السائق بيده
                'online_earnings'    => (float) $onlineEarnings,    // أجر التوصيل المستحق للسائق من الطلبات الإلكترونية
                'total_earnings'     => (float) $totalEarnings,    // إجمالي كل أرباح السائق من التوصيل
                
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
}
