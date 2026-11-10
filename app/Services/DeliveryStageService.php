<?php

namespace App\Services;

use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * DeliveryStageService — مراحل التوصيل للمندوب (البند 4 + البند 9).
 *
 * التسلسل: accepted → heading_to_trader → picked_up → out_for_delivery → delivered
 *
 *  - accepted: يضعه DriverAssignmentService::acceptOffer (الحالة تبقى 3 والمندوب معيّن).
 *  - heading_to_trader / picked_up: تسجيل مراحل فقط، الحالة تبقى 3.
 *  - out_for_delivery: ينقل الطلب للحالة 4 ويُرسل كود التسليم لجوال العميل.
 *  - delivered: عبر DriverOrdersApiController::deliver_Order بكود التسليم.
 */
class DeliveryStageService
{
    public const ADVANCE_STAGES = ['heading_to_trader', 'picked_up', 'out_for_delivery'];

    public function advance(Order $order, User $driver, string $stage): Order
    {
        if (!in_array($stage, self::ADVANCE_STAGES, true)) {
            throw new InvalidArgumentException(__('api.Delivery_Stage_Invalid'));
        }

        $updated = DB::transaction(function () use ($order, $driver, $stage) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$locked
                || (int) $locked->driver_id !== (int) $driver->id
                || $locked->delivery_type !== 'delivery') {
                throw new InvalidArgumentException(__('api.Order_Not_Found'));
            }

            if ((int) $locked->status_id !== 3) {
                throw new InvalidArgumentException(__('api.Delivery_Stage_Not_Allowed'));
            }

            $current = $locked->delivery_stage ?? 'accepted';

            $allowedFrom = match ($stage) {
                'heading_to_trader' => ['accepted'],
                'picked_up'         => ['accepted', 'heading_to_trader'],
                'out_for_delivery'  => ['picked_up'],
            };

            if (!in_array($current, $allowedFrom, true)) {
                throw new InvalidArgumentException(__('api.Delivery_Stage_Invalid'));
            }

            $data = ['delivery_stage' => $stage];

            if ($stage === 'picked_up') {
                $data['driver_at_trader_at']  = $locked->driver_at_trader_at ?? now();
                $data['driver_picked_up_at']  = now();
            }

            if ($stage === 'out_for_delivery') {
                $data['status_id'] = 4; // OrderObserver يضبط out_for_delivery_at ويرسل إشعارات الحالة
            }

            $locked->update($data);

            return $locked;
        });

        $this->notifyClient($updated, $stage);

        return $updated;
    }

    /**
     * إشعار العميل بمرحلة التوصيل. عند الخروج للتوصيل يُرسل كود التسليم (SMS + إشعار).
     */
    public function notifyClient(Order $order, string $stage): void
    {
        $client = $order->user;
        if (!$client) {
            return;
        }

        if ($stage === 'out_for_delivery') {
            if ($order->delivery_pin) {
                $this->sendCode($order, $order->delivery_pin);
            }
            return;
        }

        $messageKey = match ($stage) {
            'accepted'          => 'client_driver_accepted_message',
            'heading_to_trader' => 'client_driver_heading_message',
            'picked_up'         => 'client_order_picked_up_message',
            default             => null,
        };

        if (!$messageKey) {
            return;
        }

        $this->pushToClient($order, __('notifications.' . $messageKey, ['order_id' => $order->order_number]), $stage);
    }

    /**
     * كود الاستلام من التاجر (طلب استلام): يصل جوال العميل ويعطيه للتاجر عند الاستلام.
     */
    public function sendPickupCode(Order $order): void
    {
        if ($order->delivery_type === 'pickup' && $order->trader_pin) {
            $this->sendCode($order, $order->trader_pin);
        }
    }

    private function sendCode(Order $order, string $pin): void
    {
        $client = $order->user;
        if (!$client) {
            return;
        }

        $message = __('notifications.client_delivery_code_message', [
            'order_id' => $order->order_number,
            'pin'      => $pin,
        ]);

        if ($client->mobile) {
            try {
                app(SmsService::class)->send($client->mobile, $message);
            } catch (\Throwable $e) {
                Log::error("Delivery code SMS failed: order={$order->id} error={$e->getMessage()}");
            }
        }

        $this->pushToClient($order, $message, 'delivery_code');
    }

    private function pushToClient(Order $order, string $message, string $stage): void
    {
        $client = $order->user;
        if (!$client || !$client->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg(
                $client->id,
                $client->device_token,
                __('notifications.order_status_changed_title'),
                $message,
                [
                    'type'           => 'order_status',
                    'order_id'       => (string) $order->id,
                    'delivery_stage' => $stage,
                ]
            );
        } catch (\Throwable $e) {
            Log::error("FCM delivery stage failed: order={$order->id} error={$e->getMessage()}");
        }
    }
}
