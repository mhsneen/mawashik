<?php

namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderstatus extends Model
{
    use HasFactory;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];
     protected $hidden=['translations'];



}
