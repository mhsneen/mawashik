<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = [
        'phone', 'lat', 'lng', 'is_main', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_main'   => 'boolean',
        'is_active' => 'boolean',
        'lat'       => 'float',
        'lng'       => 'float',
    ];

    /**
     * الحقول المترجَمة - تُقرأ/تُكتب تلقائياً من جدول branch_translations عبر الحزمة.
     * تُستخدم كالتالي:
     *   $branch->name                      // باللغة الحالية للتطبيق (app()->getLocale())
     *   $branch->translate('en')->name      // بلغة محددة
     *   $branch->translateOrDefault('en')->name
     */
    public array $translatedAttributes = ['name', 'address'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /** كل الطلبات المرتبطة بهذا الفرع (يعمل فقط بعد وجود عمود branch_id على جدول orders). */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
