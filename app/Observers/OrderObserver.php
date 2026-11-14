<?php

namespace App\Observers;

use App\Models\{Order, WalletTransaction};
use App\Services\{FCMServices, LoyaltyPointsService, DriverAssignmentService, AdminNotifier, DepositService, CommissionService};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order): void
    {
        // ملاحظة: العربون والعمولة لا يُحسبان هنا لأن الإجماليات تكون صفراً عند created.
        // يُحسبان عبر DepositService::applyToOrder بعد تثبيت الإجماليات (CartApiController::store).

        // ✅ نرسل إشعار "استلام الطلب" فقط للطلبات الكاش (التي تُنشأ بـ status_id = 1)
        if ($order->payment_method === 'cash' && $order->status_id == 1) {
            // 1. إشعار الأدمن
            AdminNotifier::newOrder($order);

            // 2. إشعار العميل عبر FCM
            $this->notifyClient($order,
                __('notifications.order_received_title'),
                __('notifications.order_received_message', ['order_id' => $order->order_number])
            );
        }

        // ✅ إشعار التاجر بوجود طلب جديد — فقط للطلبات الجاهزة للتنفيذ (status 1).
        // الطلب الإلكتروني (status 7) لا يصل للتاجر قبل نجاح الدفع (انظر updated()).
        if ((int) $order->status_id === 1) {
            $this->notifyTrader($order,
                __('notifications.trader_new_order_title'),
                __('notifications.trader_new_order_message', ['order_id' => $order->order_number]),
                ['type' => 'new_trader_order', 'order_id' => (string) $order->id]
            );
        }
    }

    public function updated(Order $order): void
    {
        if (!$order->isDirty('status_id')) {
            // ✅ إشعار أدمن عند نجاح الدفع الإلكتروني
            if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
                AdminNotifier::orderPaidOnline($order);
            }
            return;
        }

        $newStatus = (int) $order->status_id;
        $oldStatus = (int) $order->getOriginal('status_id');

        // === 1) تحديث الـ Timestamps المناسبة ===
        $timestampColumn = match ($newStatus) {
            3 => 'prepared_at',
            4 => 'out_for_delivery_at',
            5 => 'delivered_at',
            default => null,
        };

        if ($timestampColumn && !$order->{$timestampColumn}) {
            $order->{$timestampColumn} = now();
        }

        // === 2) تتبع ذهاب المندوب للتاجر ===
        if ($newStatus === 4 && $order->pickup_type === 'pickup' && !$order->driver_at_trader_at) {
            $order->driver_at_trader_at = now();
        }

        // === 2.5) نجاح الدفع الإلكتروني (7 → 1): تسجيل العربون وإشعار التاجر ===
        if ($newStatus === 1 && $oldStatus === 7 && $order->hasSecuredPayment()) {
            app(DepositService::class)->reserve($order);

            $this->notifyTrader($order,
                __('notifications.trader_new_order_title'),
                __('notifications.trader_new_order_message', ['order_id' => $order->order_number]),
                ['type' => 'new_trader_order', 'order_id' => (string) $order->id]
            );
        }

        // === 3) منطق خاص بكل حالة ===
        switch ($newStatus) {
            case 3: // جاهز للاستلام
                // تعيين سائق متاح فور جهوزية الطلب
                if ($order->delivery_type === 'delivery' && is_null($order->driver_id)) {
                    (new DriverAssignmentService())->broadcastToAvailableDrivers($order);
                }

                // طلب استلام من التاجر: كود التسليم يصل جوال العميل ليعطيه للتاجر
                if ($order->delivery_type === 'pickup') {
                    app(\App\Services\DeliveryStageService::class)->sendPickupCode($order);
                }
                break;

            case 5: // تم التسليم
                AdminNotifier::orderDelivered($order);

                // الدفع الكاش عند الاستلام
                if ($order->payment_method === 'cash' && $order->payment_status !== 'paid') {
                    $order->payment_status = 'paid';
                    $order->paid_at = now();
                }

                // عمولة المندوب + إطلاق العربون للتاجر ثم تسوية باقي المستحقات
                // (الترتيب مهم: الإطلاق أولاً حتى تخصم التسوية ما أُطلق مسبقاً)
                $order->commission_driver = app(CommissionService::class)->calculateForDriver($order);
                app(DepositService::class)->releaseToTrader($order);
                app(CommissionService::class)->settleOnDelivery($order);

                // نقاط الولاء
                if (is_null($order->points_awarded_at)) {
                    $this->awardLoyaltyPoints($order);
                }
                break;

            case 6: // ملغي
                // فشل الدفع الإلكتروني (7 → 6 بحالة دفع failed): الطلب لم يُدفع ولم يصل للتاجر،
                // فلا عربون يُستردّ/يُصادَر ولا مبلغ يُرجَع ولا إشعار إلغاء للإدارة.
                $paymentFailed = $oldStatus === 7 && !$order->hasSecuredPayment();
                if ($paymentFailed) {
                    break;
                }

                AdminNotifier::orderCancelled($order);

                // معالجة العربون (الدوال تتجاهل الطلب إن لم يكن عربونه مدفوعاً فعلاً)
                $depositService = app(DepositService::class);
                if ($order->isDepositRefundable($oldStatus)) {
                    $depositService->refundToCustomer($order);
                } else {
                    $depositService->forfeitToPlatform($order);
                }

                // استرداد المبلغ المتبقي إذا كان الطلب مدفوعاً مسبقاً (محفظة/إلكتروني)
                if ($order->payment_status === 'paid' && $order->payment_method !== 'cash') {
                    $this->refundRemainingToClient($order);
                }
                break;
        }

        // حفظ واحد فقط بدل 4-5 مرات
        $order->saveQuietly();

        // === 4) الإشعارات (FCM) ===
        $this->dispatchStatusNotifications($order, $newStatus, $oldStatus);

        // === 5) إشعار أدمن عند نجاح الدفع الإلكتروني ===
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            AdminNotifier::orderPaidOnline($order);
        }
    }

    /**
     * إرسال إشعارات FCM حسب الحالة لكل من: العميل، التاجر، المندوب.
     */
    private function dispatchStatusNotifications(Order $order, int $newStatus, int $oldStatus): void
    {
        // ===== 1) إشعار العميل =====
        if ($newStatus === 1 && $oldStatus === 7) {
            $this->notifyClient($order,
                __('notifications.order_received_title'),
                __('notifications.order_received_message', ['order_id' => $order->order_number])
            );
        } elseif (in_array($newStatus, [2, 3, 4, 5, 6])) {
            $statusName = $order->status?->name ?? '';
            $this->notifyClient($order,
                __('notifications.order_status_changed_title'),
                __('notifications.order_status_changed_message', [
                    'order_id' => $order->order_number,
                    'status'   => $statusName,
                ]),
                ['type' => 'order_status', 'order_id' => (string) $order->id, 'status_id' => (string) $newStatus]
            );
        }

        // ===== 2) إشعار التاجر =====
        // الحالة 1 (جديد) تُعالج في created() / عند نجاح الدفع — هنا نعالج باقي الحالات.
        // الطلب الذي فشل دفعه (7 → 6) لم يصل للتاجر أصلاً فلا نشعره بإلغائه.
        $neverReachedTrader = $newStatus === 6 && $oldStatus === 7 && !$order->hasSecuredPayment();

        if (!$neverReachedTrader && in_array($newStatus, [2, 3, 4, 5, 6])) {
            $traderTitle = match ($newStatus) {
                2 => __('notifications.trader_order_accepted_title'),
                3 => __('notifications.trader_order_preparing_title'),
                4 => __('notifications.trader_order_out_for_delivery_title'),
                5 => __('notifications.trader_order_delivered_title'),
                6 => __('notifications.trader_order_cancelled_title'),
                default => __('notifications.order_status_changed_title'),
            };

            $traderMessage = match ($newStatus) {
                2 => __('notifications.trader_order_accepted_message', ['order_id' => $order->order_number]),
                3 => __('notifications.trader_order_preparing_message', ['order_id' => $order->order_number]),
                4 => __('notifications.trader_order_out_for_delivery_message', ['order_id' => $order->order_number]),
                5 => __('notifications.trader_order_delivered_message', ['order_id' => $order->order_number]),
                6 => __('notifications.trader_order_cancelled_message', ['order_id' => $order->order_number]),
                default => '',
            };

            $this->notifyTrader($order, $traderTitle, $traderMessage, [
                'type'     => 'order_status',
                'order_id' => (string) $order->id,
                'status_id'=> (string) $newStatus,
            ]);
        }

        // ===== 3) إشعار المندوب =====
        if ($order->driver_id && in_array($newStatus, [3, 4, 5, 6])) {
            $driverTitle = match ($newStatus) {
                3 => __('notifications.driver_order_ready_title'),
                4 => __('notifications.driver_out_for_delivery_title'),
                5 => __('notifications.driver_order_delivered_title'),
                6 => __('notifications.driver_order_cancelled_title'),
                default => __('notifications.order_status_changed_title'),
            };

            $driverMessage = match ($newStatus) {
                3 => __('notifications.driver_order_ready_message', ['order_id' => $order->order_number]),
                4 => __('notifications.driver_out_for_delivery_message', ['order_id' => $order->order_number]),
                5 => __('notifications.driver_order_delivered_message', ['order_id' => $order->order_number]),
                6 => __('notifications.driver_order_cancelled_message', ['order_id' => $order->order_number]),
                default => '',
            };

            $this->notifyDriver($order, $driverTitle, $driverMessage, [
                'type'     => 'order_status',
                'order_id' => (string) $order->id,
                'status_id'=> (string) $newStatus,
            ]);
        }
    }

    /**
     * إشعار العميل عبر FCM.
     */
    private function notifyClient(Order $order, string $title, string $body, array $data = []): void
    {
        $user = $order->user;
        if (!$user || !$user->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg($user->id, $user->device_token, $title, $body, $data);
        } catch (\Throwable $e) {
            Log::error("FCM to client failed: order={$order->id} error={$e->getMessage()}");
        }
    }

    /**
     * إشعار التاجر عبر FCM.
     */
    private function notifyTrader(Order $order, string $title, string $body, array $data = []): void
    {
        $trader = $order->trader;
        if (!$trader || !$trader->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg($trader->id, $trader->device_token, $title, $body, $data);
        } catch (\Throwable $e) {
            Log::error("FCM to trader failed: order={$order->id} error={$e->getMessage()}");
        }
    }

    /**
     * إشعار المندوب عبر FCM.
     */
    private function notifyDriver(Order $order, string $title, string $body, array $data = []): void
    {
        $driver = $order->driver;
        if (!$driver || !$driver->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg($driver->id, $driver->device_token, $title, $body, $data);
        } catch (\Throwable $e) {
            Log::error("FCM to driver failed: order={$order->id} error={$e->getMessage()}");
        }
    }

    /**
     * إضافة نقاط الولاء للعميل.
     */
    private function awardLoyaltyPoints(Order $order): void
    {
        $user = $order->user;
        if (!$user) {
            return;
        }

        $loyaltyService = new LoyaltyPointsService();
        $earnedPoints = $loyaltyService->calculateEarnedPoints($order);

        if ($earnedPoints > 0) {
            $loyaltyService->addPoints(
                $user,
                $earnedPoints,
                $order,
                __('dashboard.loyalty_points_earned_description', ['order_number' => $order->order_number])
            );
            $order->earned_points = $earnedPoints;
        }

        $order->points_awarded_at = now();
    }

    /**
     * استرداد المبلغ المتبقي للعميل عند الإلغاء.
     */
    private function refundRemainingToClient(Order $order): void
    {
        $user = $order->user;
        $amount = (float) $order->remaining_amount;
        if (!$user || $amount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $order, $amount) {
            $wallet = $user->wallet()->lockForUpdate()->first();
            if (!$wallet) {
                Log::error("Wallet not found for user ID: {$user->id} while refunding order: {$order->order_number}");
                return;
            }

            $wallet->balance += $amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'        => $wallet->id,
                'order_id'         => $order->id,
                'type'             => 'credit',
                'amount'           => $amount,
                'transaction_type' => 'order_refund',
                'description'      => __('dashboard.order_refund_description', ['order_number' => $order->order_number]),
            ]);
        });
    }

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}
