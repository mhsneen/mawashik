<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Textpage extends Model
{
    use HasFactory;
    use Translatable;
    protected $with = ['translations'];
    protected $fillable = [];
    protected $translatedAttributes = ['name', 'details', 'slug'];
    protected $hidden = ['translations'];

}
