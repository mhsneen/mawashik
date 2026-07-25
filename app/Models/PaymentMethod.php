<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'is_enabled',
        'mode',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        // مشفّر تلقائياً عند الحفظ ويُفكّ تلقائياً عند القراءة - المفاتيح لا تُخزّن نصاً صريحاً في قاعدة البيانات
        'config'     => 'encrypted:array',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /** بيانات الكتالوج الثابتة لهذه البوابة (الاسم/الحقول/الرابط..) من config/payment_gateways.php */
    public function catalog(): array
    {
        return config('payment_gateways.' . $this->code, []);
    }

    public function nameFor(string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $catalog = $this->catalog();
        return $locale === 'ar'
            ? ($catalog['name_ar'] ?? $this->code)
            : ($catalog['name_en'] ?? $this->code);
    }

    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
}
