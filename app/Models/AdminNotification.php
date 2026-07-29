<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'type', 'title', 'body', 'is_read', 'read_at'];
    protected $casts = ['is_read' => 'boolean'];

    public function order() { return $this->belongsTo(Order::class); }

    public function scopeUnread($q) { return $q->where('is_read', false); }
}
