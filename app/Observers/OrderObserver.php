<?php

namespace App\Observers;

use App\Models\{Order, WalletTransaction};
use App\Services\{FCMServices, LoyaltyPointsService, DriverAssignmentService};

class OrderObserver
{
    public function created(Order $order): void
    {
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

        // ✅ تعيين سائق متاح فور وصول الطلب (إذا كان توصيل)
        // السائق المتاح = نشط وليس لديه طلب آخر قيد التوصيل حالياً
        if ($order->delivery_type === 'delivery') {
            (new DriverAssignmentService())->assignAvailableDriver($order);
        }
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status_id')) {
            $newStatus = $order->status_id;

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

            // ✅ استرداد المبلغ للمحفظة عند الإلغاء
            if ($newStatus == 6) {
                $user = $order->user;
                if ($user) {
                    $wallet = $user->wallet;
                    $amount = $order->final_price;

                    $wallet->balance += $amount;
                    $wallet->save();

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'description' => 'استرداد مبلغ الطلب #' . $order->id,
                    ]);
                }
            }

            $fcm = new FCMServices();
            $user = $order->user;
            if ($user && $user->device_token) {
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

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}
