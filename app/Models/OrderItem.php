<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'variant_label_snapshot',
        'quantity',
        'unit_price',
        'total_price',
        // === مواشي وسيط ===
        'slaughter_selected',
        'slaughter_price',
        'slaughter_total',
    ];

    protected $casts = [
        'slaughter_selected' => 'boolean',
        'slaughter_price' => 'float',
        'slaughter_total' => 'float',
    ];

    // العلاقة مع الطلب
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // العلاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // العلاقة مع الفيرنت المختار (لو المنتج فيه Variants)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // ✅ خصائص هذا المنتج داخل الطلب
    public function attributes()
    {
        return $this->hasMany(OrderItemAttributeValue::class);
    }

    public function ScopeFilterDashbordReport($query, array $filters)
    {


        $query->when($filters['search_date_from'] ?? false, function ($query, $searchprice) {

            return  $query->whereBetween('order_items.created_at', [$searchprice, request('search_date_to')]);
        });
    }




}
