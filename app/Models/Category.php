<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name', 'slug'];
    protected $fillable = ['parent_id', 'image', 'is_active'];
    protected $hidden = ['translations', 'image'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = ['image_url', 'base_image_url'];

    const FOLDER = 'categories';


    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->image) : "";
    }
    public function getBaseImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER  . '/' . $this->image) : "";
    }
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }
    public function getActive()
    {
        return $this->is_active == 0 ? __('dashboard.Inactive') : __('dashboard.effective');
    }

    public function subCategories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function sliders()
    {
        return $this->hasMany(CategorySlider::class);
    }

    public function subCategoriesapi()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->active()
            ->with(['sliders:id,category_id,image']);
    }
}
