<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUploadService;

class CategorySlider extends Model
{
    use HasFactory;

    protected $fillable = ['image', 'category_id'];
    protected $hidden = ['image'];
    protected $appends = ['image_url', 'base_image_url'];

    const FOLDER = 'categorysliders';

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->image) : "";
    }
    public function getBaseImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER  . '/' . $this->image) : "";
    }
    protected static function booted()
    {



        static::deleting(function ($items) {
            if ($items->image) {
                $imageService = app(ImageUploadService::class);
                $imageService->deleteOldImages($items->image, self::FOLDER);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
