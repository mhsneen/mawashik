<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DriverAssignmentService
{
    /**
     * إيجاد سائق متاح وتعيينه للطلب
     * السائق المتاح: is_active=1 ونختار الأقل ازدحاماً بالطلبات النشطة (1,2,3,4)
     */
    public function assignAvailableDriver(Order $order): ?User
    {
        // قائمة الحالات التي تعتبر الطلب فيها "نشطاً" ولم يكتمل بعد
        $activeStatuses = [1, 2, 3, 4];

        $driver = DB::transaction(function () use ($order, $activeStatuses) {
            // نستخدم withCount لحساب الطلبات النشطة للسائق
            $driver = User::Drivers()
                ->where('is_active', 1)
                ->withCount(['driverOrders' => function ($q) use ($activeStatuses) {
                    $q->whereIn('status_id', $activeStatuses);
                }])
                // نختار السائق الأقل ازدحاماً، وفي حال التساوي نختار الأقدم (أقل ID) كـ Round-Robin
                ->orderBy('driver_orders_count', 'asc')
                ->orderBy('id', 'asc')
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
     * (يمكن تعديل الحد الأقصى للطلبات النشطة حسب الحاجة، هنا نفترض أنه متاح إذا لم يكن قيد التوصيل)
     */
    public function isDriverAvailable(User $driver): bool
    {
        $activeStatuses = [1, 2, 3, 4];

        return $driver->is_active == 1
            && !$driver->driverOrders()
                ->whereIn('status_id', $activeStatuses)
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
