<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * NeighborhoodTranslation — جدول ترجمة الأحياء (عربي/إنجليزي).
 *
 * يُستخدم تلقائياً من Astrotomic/laravel-translatable عبر Neighborhood model.
 *
 * ملاحظة تسمية: استخدمنا NeighborhoodTranslation (الصيغة الإنجليزية الكاملة).
 * لو فضّلت تطابق migration 100% (neighborhood_translations) يمكن إما:
 *  - ترك هذا الملف كما هو (Laravel سيحوله تلقائياً إلى neighborhood_translations).
 *  - أو تعيين $table = 'neighborhood_translations' صراحة (مُفعّل بالفعل أدناه).
 */
class NeighborhoodTranslation extends Model
{
    use HasFactory;

    protected $table = 'neighborhood_translations';

    protected $fillable = ['name'];
    public $timestamps = false;
}
