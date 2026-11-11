<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'driver_id',
        'rating',
        'comment',
        'is_hidden',
        'order_id',
    ];

    protected $casts = ['is_hidden' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
