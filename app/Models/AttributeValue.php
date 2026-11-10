<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['value'];
    protected $fillable = ['attribute_id', 'color_code'];
    protected $hidden = ['translations'];



    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_value_product')
            ->withPivot(['price', 'quantity', 'is_default', 'sort_order'])
            ->withTimestamps();
    }
}
