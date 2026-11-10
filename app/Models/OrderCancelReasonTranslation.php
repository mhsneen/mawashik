<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancelReasonTranslation extends Model
{
    use HasFactory;
    protected $fillable=['name'];
    public $timestamps=false;
}
