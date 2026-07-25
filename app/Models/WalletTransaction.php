<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'order_id',
        'type',
        'amount',
        'description'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function getTypeTranslatedAttribute()
    {
        return match ($this->type) {
            'credit' => __('dashboard.Credit'),
            'debit'  => __('dashboard.Debit'),
            'refund' => __('dashboard.Refund'),
            default   => $this->type,
        };
    }
    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'credit' => 'green',
            'debit'  => 'red',
            'refund' => 'orange',
            default  => 'gray',
        };
    }


    public function getAmountSignedAttribute()
    {
        return match ($this->type) {
            'credit', 'refund' => ' + ' . number_format($this->amount, 2),
            'debit' => ' - ' . number_format($this->amount, 2),
            default => number_format($this->amount, 2),
        };
    }
}
