<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * تُستخدم كخدمة موحّدة (نفس فكرة ProductPriceCalculator / DeliveryPriceCalculator)
 * لفحص حالة المتجر قبل تنفيذ أي عملية تعتمد على كونه "شغال":
 * - الصيانة (appClosed): تمنع كل شي — مستخدمة بميدلوير عام على كل الـ API.
 * - أيام وساعات العمل (open_work/days_work/start_work/end_work): تمنع الطلبات
 *   بس (checkout) — التصفح يفضل شغال حتى المتجر مقفول وقتياً.
 *
 * start_work/end_work مخزّنين كـ JSON لكل يوم بالشكل:
 *   {"saturday": "08:00", "sunday": "08:00", ...}
 * وdays_work مصفوفة بأسماء الأيام المفعّلة: ["saturday", "sunday", ...]
 */
class StoreStatusService
{
    /**
     * @throws InvalidArgumentException لو التطبيق بوضع الصيانة
     */
    public function assertNotUnderMaintenance(): void
    {
        if ($this->isUnderMaintenance()) {
            throw new InvalidArgumentException($this->maintenanceMessage());
        }
    }

    /**
     * @throws InvalidArgumentException لو خارج أيام/ساعات العمل
     */
    public function assertWithinWorkingHours(?string $dateTime = null): void
    {
        if (!$this->isOpenNow($dateTime)) {
            throw new InvalidArgumentException($this->closingMessage());
        }
    }

    public function isUnderMaintenance(): bool
    {
        return Setting::where('key', 'appClosed')->value('plain_value') == 1;
    }

    public function maintenanceMessage(): string
    {
        $setting = Setting::where('key', 'appClosedText')->first();
        return $setting?->value ?: __('api.App_Under_Maintenance');
    }

    /**
     * هل المتجر مفتوح الآن (أو بتاريخ/وقت معيّن لو أُرسل)؟
     */
    public function isOpenNow(?string $dateTime = null): bool
    {
        $openWork = Setting::where('key', 'open_work')->value('plain_value');

        // لو خاصية تقييد أيام/ساعات العمل غير مفعّلة أصلاً، المتجر يُعتبر مفتوح دايماً
        if ($openWork != 1) {
            return true;
        }

        $checkAt = $dateTime ? Carbon::parse($dateTime) : now();
        $dayKey = strtolower($checkAt->format('l'));

        $daysWork = $this->decodeArray(Setting::where('key', 'days_work')->value('plain_value'));

        if (!in_array($dayKey, $daysWork)) {
            return false;
        }

        $startWork = $this->decodeArray(Setting::where('key', 'start_work')->value('plain_value'));
        $endWork = $this->decodeArray(Setting::where('key', 'end_work')->value('plain_value'));

        $start = $startWork[$dayKey] ?? null;
        $end = $endWork[$dayKey] ?? null;

        if (!$start || !$end) {
            return false;
        }

        $startDateTime = Carbon::parse($checkAt->format('Y-m-d') . ' ' . $start);
        $endDateTime = Carbon::parse($checkAt->format('Y-m-d') . ' ' . $end);

        return $checkAt->between($startDateTime, $endDateTime);
    }

    public function closingMessage(): string
    {
        $setting = Setting::where('key', 'out_of_working_hours_message')->first();
        return $setting?->value ?: __('api.Store_Closed_Now');
    }

    /**
     * ملخص كامل لحالة المتجر — تُستخدم بواجهة عرض حالة المتجر بالتطبيق
     * (بانر "المتجر مغلق حالياً"، تعطيل زر الطلب، عرض أوقات العمل...).
     */
    public function getStatus(): array
    {
        $daysWork = $this->decodeArray(Setting::where('key', 'days_work')->value('plain_value'));
        $startWork = $this->decodeArray(Setting::where('key', 'start_work')->value('plain_value'));
        $endWork = $this->decodeArray(Setting::where('key', 'end_work')->value('plain_value'));

        $schedule = collect($daysWork)->mapWithKeys(fn($day) => [
            $day => [
                'start' => $startWork[$day] ?? null,
                'end' => $endWork[$day] ?? null,
            ],
        ]);

        return [
            'is_maintenance' => $this->isUnderMaintenance(),
            'maintenance_message' => $this->isUnderMaintenance() ? $this->maintenanceMessage() : null,
            'is_open_now' => $this->isOpenNow(),
            'closing_message' => ! $this->isOpenNow() ? $this->closingMessage() : null,
            'working_days' => array_values($daysWork),
            'schedule' => $schedule,
        ];
    }

    /**
     * القيم مخزّنة أحياناً كـ JSON نصّي وأحياناً array جاهز (حسب كيف اتحفظت) —
     * هذي تتعامل مع الحالتين بأمان.
     */
    protected function decodeArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
