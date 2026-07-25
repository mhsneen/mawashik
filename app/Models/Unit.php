<?php

namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use Translatable;
    use SoftDeletes;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];
     protected $hidden=['translations'];



}
