<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['product_id', 'combination_key', 'sku', 'price', 'quantity', 'image', 'is_active'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // يبني مفتاح فريد وثابت من قائمة attribute_value_id (بغض النظر عن ترتيب الإدخال)
    public static function makeCombinationKey(array $attributeValueIds): string
    {
        $ids = array_map('intval', $attributeValueIds);
        sort($ids);
        return implode('_', $ids);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value');
    }

    // السعر الفعلي - يرجع لسعر المنتج لو ما فيه سعر خاص
    public function getEffectivePriceAttribute()
    {
        return $this->price ?? $this->product->netPrice;
    }

    public function getEffectiveQuantityAttribute()
    {
        return $this->quantity ?? $this->product->quantity;
    }

    public function hasStock(int $qty = 1): bool
    {
        return $this->effective_quantity >= $qty;
    }
}
