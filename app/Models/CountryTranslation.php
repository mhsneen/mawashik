<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CountryTranslation — جدول ترجمة الدول (عربي/إنجليزي).
 *
 * يُستخدم تلقائياً من Astrotomic/laravel-translatable عبر Country model.
 * $timestamps = false لأن جدول الترجمة لا يحوي created_at/updated_at.
 */
class CountryTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;
}
