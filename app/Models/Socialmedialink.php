<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUploadService;


class Socialmedialink extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'icon', 'link'];
    const FOLDER = 'socialmedialinks';
    protected $appends = ['icon_url'];

    public function getIconUrlAttribute()
    {
        return $this->icon ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->icon) : "";
    }

    protected static function booted()
    {
        static::deleting(function ($items) {
            if ($items->icon) {
                $imageService = app(ImageUploadService::class);
                $imageService->deleteOldImages($items->icon, self::FOLDER);
            }
        });
    }
}
