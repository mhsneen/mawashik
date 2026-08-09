<?php

namespace App\Services;

use App\Models\{Order, OrderDriverOffer, User};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DriverAssignmentService
{
    /** عدد السائقين اللي يُعرض عليهم الطلب بكل دفعة */
    protected const BATCH_SIZE = 5;

    /**
     * بث الطلب لأفضل N سائقين متاحين (الأقل ازدحاماً بالطلبات النشطة).
     * أول سائق يقبل عبر acceptOffer() هو من يأخذ الطلب فعلياً.
     */
    public function broadcastToAvailableDrivers(Order $order, int $batchNumber = 1): void
    {
        $activeStatuses = [1, 2, 3, 4];

        // استبعاد أي سائق أُرسل له عرض على هذا الطلب مسبقاً (بأي دفعة)
        $alreadyOfferedIds = OrderDriverOffer::where('order_id', $order->id)->pluck('driver_id');

        $drivers = User::Drivers()
            ->where('is_active', 1)
            ->whereNotIn('id', $alreadyOfferedIds)
            ->withCount(['driverOrders' => function ($q) use ($activeStatuses) {
                $q->whereIn('status_id', $activeStatuses);
            }])
            ->orderBy('driver_orders_count', 'asc')
            ->orderBy('id', 'asc')
            ->limit(self::BATCH_SIZE)
            ->get();

        if ($drivers->isEmpty()) {
            // ما فيه سائقين متاحين — الطلب يبقى بدون سائق، الأدمن يقدر يعيّن يدوياً
            return;
        }

        $fcm = new FCMServices();

        DB::transaction(function () use ($order, $drivers, $batchNumber, $fcm) {
            foreach ($drivers as $driver) {
                OrderDriverOffer::create([
                    'order_id' => $order->id,
                    'driver_id' => $driver->id,
                    'status' => 'pending',
                    'batch_number' => $batchNumber,
                    'offered_at' => now(),
                ]);

                if ($driver->device_token) {
                    $fcm->sendMsg(
                        $driver->id,
                        $driver->device_token,
                        __('notifications.driver_order_assigned_title'),
                        __('notifications.driver_order_assigned_message', ['order_id' => $order->order_number])
                    );
                }
            }
        });
    }

    /**
     * قبول السائق للعرض — محمي ذرّياً (lockForUpdate) لمنع سائقين اثنين
     * من قبول نفس الطلب باللحظة نفسها.
     *
     * @throws InvalidArgumentException
     */
    public function acceptOffer(Order $order, User $driver): void
    {
        DB::transaction(function () use ($order, $driver) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder || $lockedOrder->status_id != 3 || $lockedOrder->driver_id) {
                throw new InvalidArgumentException(__('api.Order_Already_Taken'));
            }

            $offer = OrderDriverOffer::where('order_id', $order->id)
                ->where('driver_id', $driver->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$offer) {
                throw new InvalidArgumentException(__('api.Offer_Not_Found_Or_Expired'));
            }

            $lockedOrder->update([
                'driver_id' => $driver->id,
                'status_id' => 4,
                'out_for_delivery_at' => now(),
            ]);

            $offer->update(['status' => 'accepted', 'responded_at' => now()]);

            // باقي العروض المعلّقة لنفس الطلب تصير منتهية تلقائياً
            OrderDriverOffer::where('order_id', $order->id)
                ->where('id', '!=', $offer->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired', 'responded_at' => now()]);
        });
    }

    /**
     * رفض السائق للعرض. لو كل سائقي الدفعة الحالية رفضوا/انتهت مهلتهم،
     * يبث تلقائياً دفعة جديدة من 5 سائقين آخرين.
     *
     * @throws InvalidArgumentException
     */
    public function rejectOffer(Order $order, User $driver): void
    {
        $offer = OrderDriverOffer::where('order_id', $order->id)
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->first();

        if (!$offer) {
            throw new InvalidArgumentException(__('api.Offer_Not_Found_Or_Expired'));
        }

        $offer->update(['status' => 'rejected', 'responded_at' => now()]);

        $order->refresh();
        if ($order->driver_id) {
            return; // انتحاز الطلب لسائق تاني أصلاً
        }

        $stillPending = OrderDriverOffer::where('order_id', $order->id)->pending()->exists();

        if (!$stillPending) {
            $nextBatch = OrderDriverOffer::where('order_id', $order->id)->max('batch_number') + 1;
            $this->broadcastToAvailableDrivers($order, $nextBatch);
        }
    }

    public function notifyDriverAssigned(User $driver, Order $order): void
    {
        if ($driver->device_token) {
            (new FCMServices())->sendMsg(
                $driver->id,
                $driver->device_token,
                __('notifications.driver_order_assigned_title'),
                __('notifications.driver_order_assigned_message', ['order_id' => $order->order_number])
            );
        }
    }
}
