<?php

namespace App\Observers;

use App\Models\{Order, WalletTransaction};
use App\Services\{FCMServices, LoyaltyPointsService, DriverAssignmentService, AdminNotifier};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order): void
    {
        // ✅ نرسل إشعار "استلام الطلب" فقط للطلبات الكاش (التي تُنشأ بـ status_id = 1)
        if ($order->payment_method === 'cash' && $order->status_id == 1) {

            // 1. إشعار الأدمن (النظام الجديد الموحد)
            AdminNotifier::newOrder($order);

            // 2. إشعار العميل عبر FCM
            $fcm = new FCMServices();
            $user = $order->user;
            if ($user && $user->device_token) {
                $fcm->sendMsg(
                    $user->id,
                    $user->device_token,
                    __('notifications.order_received_title'),
                    __('notifications.order_received_message', ['order_id' => $order->order_number])
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

            // ✅ إشعار الأدمن عند تسليم الطلب
            if ($newStatus == 5) {
                AdminNotifier::orderDelivered($order);
            }

            // ✅ تعيين سائق متاح فور جهوزية الطلب (الحالة 3 = جاهز للاستلام)
            if ((int)$newStatus == 3 && $order->delivery_type === 'delivery' && is_null($order->driver_id)) {
                (new DriverAssignmentService())->broadcastToAvailableDrivers($order);
            }

            // ✅ منطق الدفع الكاش عند الاستلام (Pickup or Delivery)
            if ($newStatus == 5 && $order->payment_method === 'cash' && $order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $order->paid_at = now();
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
                            __('dashboard.loyalty_points_earned_description', [
                                'order_number' => $order->order_number,
                            ])
                        );
                        $order->earned_points = $earnedPoints;
                    }

                    $order->points_awarded_at = now();
                    $order->saveQuietly();
                }
            }

            // ✅ إشعار الأدمن واسترداد المبلغ للمحفظة عند الإلغاء (بأمان تام)
            if ($newStatus == 6) {
                
                // 1. إرسال إشعار للأدمن بإلغاء الطلب
                AdminNotifier::orderCancelled($order);

                // 2. استرداد المبلغ إذا كان مدفوعاً مسبقاً
                if ($order->payment_status === 'paid') {
                    $user = $order->user;
                    if ($user) {
                        $amount = $order->final_price;

                        DB::transaction(function () use ($user, $order, $amount) {
                            $wallet = $user->wallet()->lockForUpdate()->first();

                            if (!$wallet) {
                                Log::error("Wallet not found for user ID: {$user->id} while refunding order ID: {$order->order_number}");
                                return;
                            }

                            $wallet->balance += $amount;
                            $wallet->save();

                            WalletTransaction::create([
                                'wallet_id' => $wallet->id,
                                'order_id' => $order->id,
                                'type' => 'credit',
                                'amount' => $amount,
                                'description' => __('dashboard.order_refund_description', [
                                    'order_number' => $order->order_number,
                                ]),
                            ]);
                        });
                    }
                }
            }

            $fcm = new FCMServices();
            $user = $order->user;

            // ✅ منطق الإشعارات للعميل بناءً على تغير الحالة
            if ($user && $user->device_token) {
                if ($newStatus == 1 && $oldStatus == 7) {
                    $fcm->sendMsg(
                        $user->id,
                        $user->device_token,
                        __('notifications.order_received_title'),
                        __('notifications.order_received_message', ['order_id' => $order->order_number])
                    );
                } elseif (in_array($newStatus, [2, 3, 4, 5, 6])) {
                    $fcm->sendMsg(
                        $user->id,
                        $user->device_token,
                        __('notifications.order_status_changed_title'),
                        __('notifications.order_status_changed_message', [
                            'order_id' => $order->order_number,
                            'status' => $order->status->name
                        ])
                    );
                }
            }
        }

        // ✅ إشعار أدمن عند نجاح الدفع الإلكتروني
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            AdminNotifier::orderPaidOnline($order);
        }
    }

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}