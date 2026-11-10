<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    use HasFactory, Translatable;

    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];

    protected $fillable = ['is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
