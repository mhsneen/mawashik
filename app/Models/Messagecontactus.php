<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messagecontactus extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'contactusmessages';
    protected $fillable = ['name', 'mobile', 'email', 'subject', 'details', 'is_read'];
    protected $casts = [
        'is_read' => 'boolean'
    ];




    public function scopeNotread($query)
    {
        return $query->where('is_read', 0);
    }
}
