<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUploadService;


class Onboarding extends Model
{
    use HasFactory;
    use Translatable;
    protected $with = ['translations'];
    protected $fillable = ['image'];
    protected $translatedAttributes = ['name',  'slug', 'details'];
    protected $hidden = ['translations', 'image'];
    protected $appends = ['image_url', 'base_image_url'];

    const FOLDER = 'onboardings';

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
}
