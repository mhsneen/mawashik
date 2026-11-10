<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];
    protected $fillable = ['type','is_variation'];

    // مثلاً: "المقاس" و"اللون" = true (تولّد variants)
// "الخامة" = false (وصفية فقط، تستخدم pivot العادي)

    protected $hidden = ['translations'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    // خصائص تولّد Variants (SKU/سعر/مخزون مستقل لكل تركيبة) — مقاس، لون، وزن..
    public function scopeVariation($query)
    {
        return $query->where('is_variation', true);
    }

    // خصائص تُستخدم كإضافات (Modifiers) تُحسب فوق سعر المنتج — بطاطا، صوص..
    public function scopeModifier($query)
    {
        return $query->where('is_variation', false);
    }
}
