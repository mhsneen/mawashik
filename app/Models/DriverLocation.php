<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * DriverLocation — آخر موقع معروف للمندوب (Live Tracking).
 *
 * - جدول مرتبط 1-1 مع users (unique على driver_id).
 * - يُحدَّث من تطبيق المندوب كل بضع ثوانٍ أثناء التوصيل.
 * - يُقرأ من تطبيق العميل في شاشة "تتبع الطلب".
 */
class DriverLocation extends Model
{
    use HasFactory;

    protected $table = 'driver_locations';

    protected $fillable = [
        'driver_id',
        'lat',
        'lng',
        'heading',
        'speed',
        'located_at',
    ];

    protected $casts = [
        'lat'        => 'float',
        'lng'        => 'float',
        'heading'    => 'float',
        'speed'      => 'float',
        'located_at' => 'datetime',
    ];

    /**
     * المندوب صاحب هذا الموقع.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * تحديث أو إنشاء موقع المندوب (upsert).
     */
    public static function updateOrCreateFor(int $driverId, float $lat, float $lng, ?float $heading = null, ?float $speed = null): self
    {
        return static::updateOrCreate(
            ['driver_id' => $driverId],
            [
                'lat'        => $lat,
                'lng'        => $lng,
                'heading'    => $heading,
                'speed'      => $speed,
                'located_at' => now(),
            ]
        );
    }
}
