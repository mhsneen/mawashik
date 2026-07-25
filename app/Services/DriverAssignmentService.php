<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DriverAssignmentService
{
    /**
     * إيجاد أول سائق متاح وتعيينه للطلب
     * السائق المتاح: is_active=1 وليس لديه طلب بحالة 4 (قيد التوصيل)
     */
    public function assignAvailableDriver(Order $order): ?User
    {
        // نستخدم transaction + lockForUpdate على صفوف السائقين المرشّحين
        // حتى لا يحصل تعيين نفس السائق لطلبين مختلفين في نفس اللحظة
        // (مثلاً لو عدة طلبات وصلت لحالة "تم التجهيز" في نفس الوقت).
        $driver = DB::transaction(function () use ($order) {
            $driver = User::Drivers()
                ->where('is_active', 1)
                ->whereDoesntHave('driverOrders', function ($q) {
                    $q->where('status_id', 4); // قيد التوصيل فعلاً
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if ($driver) {
                $order->update(['driver_id' => $driver->id]);
            }

            return $driver;
        });

        if ($driver) {
            $this->notifyDriverAssigned($driver, $order);
        }

        return $driver;
    }

    /**
     * هل السائق متاح لطلب جديد؟
     */
    public function isDriverAvailable(User $driver): bool
    {
        return $driver->is_active == 1
            && !$driver->driverOrders()
                ->where('status_id', 4)
                ->exists();
    }

    /**
     * إشعار السائق بأنه عُيِّن لطلب جديد
     */
    public function notifyDriverAssigned(User $driver, Order $order): void
    {
        if ($driver->device_token) {
            (new FCMServices())->sendMsg(
                $driver->id,
                $driver->device_token,
                __('notifications.driver_order_assigned_title'),
                __('notifications.driver_order_assigned_message', ['order_id' => $order->id])
            );
        }
    }
}
