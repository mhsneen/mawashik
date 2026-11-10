<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];
    protected $fillable = ['image', 'is_active'];
    protected $hidden = ['translations', 'image'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = ['image_url', 'base_image_url'];

    const FOLDER = 'brands';

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->image) : "";
    }
    public function getBaseImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER  . '/' . $this->image) : "";
    }
    public function getActive()
    {
        return $this->is_active == 0 ? __('dashboard.Inactive') : __('dashboard.effective');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
