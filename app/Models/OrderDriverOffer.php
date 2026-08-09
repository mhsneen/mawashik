<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDriverOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'driver_id', 'status', 'batch_number', 'offered_at', 'responded_at',
    ];

    protected $casts = [
        'offered_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
