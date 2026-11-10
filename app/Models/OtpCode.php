<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;
protected $fillable = [
        'verifiable_id',
        'verifiable_type',
        'purpose',
        'method',
        'contact',
        'code',
        'is_used',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function verifiable()
    {
        return $this->morphTo();
    }

    // دالة للتحقق من الانتهاء
    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }

    // ثوابت لتفادي الأخطاء الإملائية في الكود
    const PURPOSE_REGISTRATION = 'registration';
    const PURPOSE_LOGIN = 'login';
    const PURPOSE_PASSWORD_RESET = 'password_reset';


}
