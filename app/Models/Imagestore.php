<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagestore extends Model
{
    use HasFactory;
    protected $table = 'imagesstore';
    protected $fillable = ['filename', 'extension', 'size'];
    /**
     * Get the owning imageable model.
     */
    protected $hidden = [
        'filename',
        'extension',
        'size',
        'imageable_type',
        'imageable_id',
        'created_at',
        'updated_at',
        'imageable',
    ];
    protected $appends = ['image_url',  'base_image_url'];

    public function imageable()
    {
        return $this->morphTo();
    }
    // رابط الصورة
    public function getBaseImageUrlAttribute()
    {
        return asset('uploads/imagestore/' . $this->filename);
    }

    public function getImageUrlAttribute()
    {
        return asset('uploads/imagestore/thumbnail/' . $this->filename);
    }
}
