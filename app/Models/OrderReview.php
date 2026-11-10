<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OrderReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'rating',
        'comment',
        'order_id'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
