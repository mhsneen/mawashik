<?php

namespace App\Observers;

use App\Models\{Order, WalletTransaction};
use App\Services\{FCMServices, LoyaltyPointsService, DriverAssignmentService};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order): void
    {
        // ✅ نرسل إشعار "استلام الطلب" فقط للطلبات الكاش (التي تُنشأ بـ status_id = 1)
        // الطلبات الإلكترونية تُنشأ بـ (status_id = 7) ويجب ألا يصدر إشعار حتى يُدفع العميل.
        if ($order->payment_method === 'cash' && $order->status_id == 1) {
            $fcm = new FCMServices();
            $user = $order->user;
            if ($user && $user->device_token) {
                $fcm->sendMsg(
                    $user->id,
                    $user->device_token,
                    __('notifications.order_received_title'),
                    __('notifications.order_received_message', ['order_id' => $order->id])
                );
            }
        }
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status_id')) {
            $newStatus = $order->status_id;
            $oldStatus = $order->getOriginal('status_id');

            $timestampColumn = match ($newStatus) {
                3 => 'prepared_at',
                4 => 'out_for_delivery_at',
                5 => 'delivered_at',
                default => null,
            };

            if ($timestampColumn) {
                $order->{$timestampColumn} = now();
                $order->saveQuietly();
            }

            // ✅ تعيين سائق متاح فور جهوزية الطلب (الحالة 3 = جاهز للاستلام)
            if ($newStatus === 3 && $order->delivery_type === 'delivery' && is_null($order->driver_id)) {
                (new DriverAssignmentService())->assignAvailableDriver($order);
            }

            // ✅ إضافة نقاط الولاء عند التسليم — مع حماية من الإضافة المكررة
            if ($newStatus == 5 && is_null($order->points_awarded_at)) {
                $user = $order->user;
                if ($user) {
                    $loyaltyService = new LoyaltyPointsService();
                    $earnedPoints = $loyaltyService->calculateEarnedPoints($order);

                    if ($earnedPoints > 0) {
                        $loyaltyService->addPoints(
                            $user,
                            $earnedPoints,
                            $order,
                            'نقاط مكتسبة من الطلب #' . $order->id
                        );

                        $order->earned_points = $earnedPoints;
                    }

                    $order->points_awarded_at = now();
                    $order->saveQuietly();
                }
            }

            // ✅ استرداد المبلغ للمحفظة عند الإلغاء (بأمان تام)
            if ($newStatus == 6 && $order->payment_status === 'paid') {
                $user = $order->user;
                if ($user) {
                    $amount = $order->final_price;

                    DB::transaction(function () use ($user, $order, $amount) {
                        $wallet = $user->wallet()->lockForUpdate()->first();

                        if (!$wallet) {
                            Log::error("Wallet not found for user ID: {$user->id} while refunding order ID: {$order->id}");
                            return;
                        }

                        $wallet->balance += $amount;
                        $wallet->save();

                        WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'order_id' => $order->id,
                            'type' => 'credit',
                            'amount' => $amount,
                            'description' => 'استرداد مبلغ الطلب #' . $order->id,
                        ]);
                    });
                }
            }

            $fcm = new FCMServices();
            $user = $order->user;

            // ✅ منطق الإشعارات بناءً على تغير الحالة
            if ($user && $user->device_token) {

                // 1. إذا كان الطلب إلكترونياً وتحول من (7 بانتظار الدفع) إلى (1 جديد/مدفوع)
                if ($newStatus == 1 && $oldStatus == 7) {
                    $fcm->sendMsg(
                        $user->id,
                        $user->device_token,
                        __('notifications.order_received_title'),
                        __('notifications.order_received_message', ['order_id' => $order->id])
                    );
                }
                // 2. لباقي الحالات (جاهز، بالطريق، تم التسليم، ملغي) نرسل إشعار تغير الحالة العادي
                elseif (in_array($newStatus, [3, 4, 5, 6])) {
                    $fcm->sendMsg(
                        $user->id,
                        $user->device_token,
                        __('notifications.order_status_changed_title'),
                        __('notifications.order_status_changed_message', [
                            'order_id' => $order->id,
                            'status' => $order->status->name
                        ])
                    );
                }
            }
        }
    }

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}
