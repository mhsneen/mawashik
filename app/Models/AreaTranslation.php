<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AreaTranslation — جدول ترجمة المناطق (عربي/إنجليزي).
 *
 * يُستخدم تلقائياً من Astrotomic/laravel-translatable عبر Area model.
 */
class AreaTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;
}
