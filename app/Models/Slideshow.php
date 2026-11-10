<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUploadService;

class Slideshow extends Model
{
    use HasFactory;
    protected $fillable = ['is_active', 'image'];
    protected $hidden = ['image', 'updated_at'];
    protected $appends = ['image_url', 'base_image_url'];

    const FOLDER = 'slideshows';


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


    public function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }
}
