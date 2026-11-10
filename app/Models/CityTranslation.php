<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CityTranslation — جدول ترجمة المدن (عربي/إنجليزي).
 *
 * يُستخدم تلقائياً من Astrotomic/laravel-translatable عبر City model.
 */
class CityTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;
}
