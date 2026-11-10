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
    protected $fillable = ['product_id', 'combination_key', 'sku', 'price', 'quantity', 'image', 'is_active' ,'track_quantity'];
    protected $casts = [
        'quantity' => 'decimal:2',
        'track_quantity' => 'boolean',
        'is_active' => 'boolean',
    ];
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



public function hasStock(float $quantity = 1.00): bool
{
    if (!$this->track_quantity) {
        return true;
    }

    if (is_null($this->quantity)) {
        return true;
    }

    return (float) $this->quantity >= (float) $quantity;
}
}
