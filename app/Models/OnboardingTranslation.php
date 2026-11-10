<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'details'];
    public $timestamps = false;
}
