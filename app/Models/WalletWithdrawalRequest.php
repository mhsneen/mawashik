<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletWithdrawalRequest extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'status',
        'driver_note',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'   => __('dashboard.Withdrawal_Status_Pending'),
            'approved'  => __('dashboard.Withdrawal_Status_Approved'),
            'rejected'  => __('dashboard.Withdrawal_Status_Rejected'),
            'cancelled' => __('dashboard.Withdrawal_Status_Cancelled'),
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'approved'  => 'success',
            'rejected'  => 'danger',
            'cancelled' => 'secondary',
            default     => 'secondary',
        };
    }
}
