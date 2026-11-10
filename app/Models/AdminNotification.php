<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'type', 'title', 'body', 'url', 'icon', 'color',
        'is_read', 'read_at', 'notifiable_type', 'notifiable_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function scopeUnread($q)
    {
        return $q->where('is_read', false);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
