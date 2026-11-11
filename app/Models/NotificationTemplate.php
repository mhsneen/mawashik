<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * تجاوزات نصوص الإشعارات من لوحة التحكم.
 * تُحمَّل عند الإقلاع في الـ translator تحت المجموعة notifications فلا تحتاج مواضع الاستدعاء لأي تغيير.
 */
class NotificationTemplate extends Model
{
    protected $fillable = ['key', 'text_ar', 'text_en'];

    public const CACHE_KEY = 'notification_templates_overrides';

    protected static function booted(): void
    {
        $flush = fn() => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    /** @return array<string, array{ar: ?string, en: ?string}> */
    public static function overrides(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            if (!Schema::hasTable('notification_templates')) {
                return [];
            }
            return self::all()->mapWithKeys(fn($t) => [
                $t->key => ['ar' => $t->text_ar, 'en' => $t->text_en],
            ])->all();
        });
    }

    public static function applyToTranslator(): void
    {
        try {
            $overrides = self::overrides();
        } catch (\Throwable $e) {
            return; // قاعدة البيانات غير جاهزة (قبل الـ migrate)
        }

        foreach (['ar', 'en'] as $locale) {
            $lines = [];
            foreach ($overrides as $key => $texts) {
                if (!empty($texts[$locale])) {
                    $lines['notifications.' . $key] = $texts[$locale];
                }
            }
            if ($lines) {
                app('translator')->addLines($lines, $locale);
            }
        }
    }
}
