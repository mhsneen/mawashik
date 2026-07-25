<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItemAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'attribute_id',
        'attribute_value_id',
        'attribute_name',
        'value_name',
        'color_code',
        'price',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // ✅ علاقة attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    // ✅ علاقة attribute_value
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
