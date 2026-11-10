<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ImageUploadService;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes, Translatable;
    protected $with = ['translations'];
    protected $fillable = [
        'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
        'is_active', 'new_arrivals', 'brand_id', 'sku', 'quantity',
        'category_id', 'image', 'calories', 'unit_id', 'track_quantity',
        // === مواشي وسيط ===
        'trader_id', 'product_status', 'approved_at', 'rejected_at', 'rejection_reason',
        'slaughter_available', 'slaughter_price', 'slaughter_required',
        // === فيديو + مواصفات (البند الثالث) ===
        'video_url', 'specifications',
    ];
    protected $date = ['salePrice_start', 'salePrice_end', 'deleted_at', 'approved_at', 'rejected_at'];
    protected $translatedAttributes = ['name', 'slug', 'details', 'description'];
    protected $hidden = ['translations', 'image'];
    const FOLDER = 'products';
    protected $appends = ['image_url', 'final_price', 'is_on_sale', 'base_image_url'];

    protected $casts = [
        'netPrice' => 'float',
        'salePrice' => 'float',
        'final_price' => 'float',
        'track_quantity' => 'boolean',
        'quantity' => 'decimal:2',
        // === مواشي وسيط ===
        'slaughter_available' => 'boolean',
        'slaughter_required' => 'boolean',
        'slaughter_price' => 'float',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        // === specifications JSON ===
        'specifications' => 'array',
    ];

    // === مواشي وسيط — Scopes ===
    public function scopeApproved($query)
    {
        return $query->where('product_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('product_status', 'pending');
    }

    public function scopeForTrader($query, int $traderId)
    {
        return $query->where('trader_id', $traderId);
    }

    // المنتج متاح للعرض العام فقط: معتمد + تاجر معتمد
    public function scopeAvailableForClients($query)
    {
        return $query->where('product_status', 'approved')
            ->where('is_active', true)
            ->whereHas('trader', fn($q) => $q->where('trader_status', 'approved'));
    }

    // === مواشي وسيط — Relations ===
    // === مواشي وسيط — Relations ===
public function trader()
{
    return $this->belongsTo(User::class, 'trader_id');
}

/**
 * عناصر الطلبات المرتبطة بهذا المنتج (لمعرفة كم مرة اُشتري).
 */
public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'product_id');
}

    // === الجغرافيا تُورَّث من التاجر ===
    // المنتج لا يملك حقول city_id/area_id/neighborhood_id/lat/lng خاصة به،
    // بل يستخدم موقع التاجر عبر العلاقة $product->trader->store_lat / store_lng / city_id / area_id / neighborhood_id.
    public function getStoreLatAttribute()
    {
        return $this->trader?->store_lat;
    }

    public function getStoreLngAttribute()
    {
        return $this->trader?->store_lng;
    }

    public function getCityIdAttribute()
    {
        return $this->trader?->city_id;
    }

    public function getAreaIdAttribute()
    {
        return $this->trader?->area_id;
    }

    public function getNeighborhoodIdAttribute()
    {
        return $this->trader?->neighborhood_id;
    }

    // === سعر الذبح الفعلي للمنتج (إن لم يحدد، يستخدم الافتراضي من الإعدادات) ===
    public function getSlaughterPriceEffectiveAttribute(): float
    {
        if (!$this->slaughter_available) {
            return 0;
        }
        if (!is_null($this->slaughter_price)) {
            return (float) $this->slaughter_price;
        }
        return (float) (Setting::where('key', 'slaughter_default_price')->value('plain_value') ?? 0);
    }

    // السعر النهائي بعد الخصم
    // public function getFinalPriceAttribute()
    // {
    //     $finalPrice = $this->netPrice;

    //     if ($this->isOnSale()) {
    //         $discount = ($this->netPrice * $this->salePrice) / 100;
    //         $finalPrice = $this->netPrice - $discount;
    //     }

    //     return (float) $finalPrice;
    // }

    // هل هناك عرض حاليًا؟
    public function getIsOnSaleAttribute()
    {
        return $this->isOnSale();
    }

    // دالة مساعدة للتحقق من الخصم
    public function isOnSale()
    {
        if (
            $this->salePrice
            && $this->salePrice_start
            && $this->salePrice_end
        ) {

            $now = now();
            return $now->between($this->salePrice_start, $this->salePrice_end);
        }

        return false;
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->image) : "";
    }

    public function getBaseImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER  . '/' . $this->image) : "";
    }

    public function getFormattednetPriceAttribute()
    {
        // number_format مع إزالة الأصفار الزائدة
        return rtrim(rtrim(number_format($this->netPrice, 2, '.', ','), '0'), '.');
    }

    public function getFormattedsalePriceAttribute()
    {
        // number_format مع إزالة الأصفار الزائدة
        return rtrim(rtrim(number_format($this->salePrice, 2, '.', ','), '0'), '.');
    }




    public function getActive()
    {
        return $this->is_active == 0 ? __('dashboard.Inactive') : __('dashboard.effective');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeHomeApi($query)
    {
        return $query->where('new_arrivals', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }




    public function ScopeFilterDashbord($query, array $filters)
    {

        $query->when($filters['search_name'] ?? false, function ($query, $search) {

            return  $query->WhereTranslationLike('name', '%' . $search . '%');
        });
        $query->when($filters['search_category_id'] ?? false, function ($query, $search) {
            return  $query->where('category_id', $search);
        });
        $query->when($filters['search_trader_id'] ?? false, function ($query, $search) {
            return  $query->where('trader_id', $search);
        });


        $query->when($filters['search_active'] ?? false, function ($query, $search) {
            if ($search === '1') {
                return  $query->where('active', $search);
            } else {
                return  $query->where('active', '0');
            }
        });



        $query->when($filters['search_netPrice'] ?? false, function ($query, $searchprice) {
            if (request('search_diffrnetPrice') === '=') {
                return  $query->where('netPrice', $searchprice);
            } elseif (request('search_diffrnetPrice') === '>=') {
                return  $query->where('netPrice', '>=', $searchprice);
            } elseif (request('search_diffrnetPrice') === '<=') {
                return  $query->where('netPrice', '<=', $searchprice);
            } else {
                return  $query->where('netPrice', $searchprice);
            }
        });


        $query->when($filters['search_quantity'] ?? false, function ($query, $searchquantity) {
            if (request('search_diffrquantity') === '=') {
                return  $query->where('quantity', $searchquantity);
            } elseif (request('search_diffrquantity') === '>=') {
                return  $query->where('quantity', '>=', $searchquantity);
            } elseif (request('search_diffrquantity') === '<=') {
                return  $query->where('quantity', '<=', $searchquantity);
            } else {
                return  $query->where('quantity', $searchquantity);
            }
        });
    }



    public function getTotal($converted = true)
    {
        return $total =  $this->salePrice ?? $this->netPrice;
    }



    public function getHasSaleAttribute()
    {
        // تحقق من وجود القيم بشكل صحيح
        if (is_null($this->salePrice) || is_null($this->salePrice_start) || is_null($this->salePrice_end)) {
            return false;
        }

        $now = now();
        $start = Carbon::parse($this->salePrice_start);
        $end   = Carbon::parse($this->salePrice_end);

        // تحقق من الفترة وأن الخصم أكبر من 0
        return $this->salePrice > 0 && $now->between($start, $end);
    }


    public function getSaleValueAttribute()
    {
        // يرجع نسبة الخصم فقط إذا العرض فعال
        return $this->has_sale ? $this->salePrice : 0;
    }

    public function getCurrentPriceAttribute()
    {
        // السعر بعد الخصم إن وجد
        if ($this->has_sale) {
            return $this->netPrice - ($this->netPrice * ($this->salePrice / 100));
        }
        return $this->netPrice;
    }

    public function productimages()
    {
        return $this->morphMany(Imagestore::class, 'imageable');
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
// Scope: فقط المنتجات التي تتبع المخزون
public function scopeTracksQuantity($query)
{
    return $query->where('track_quantity', true);
}

public function inStock(): bool
{
    return $this->hasStock(0.01); // أي كمية صغيرة
}

public function outOfStock(): bool
{
    return !$this->inStock();
}
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_product')
            ->withPivot(['price', 'quantity', 'is_default', 'sort_order'])
            ->withTimestamps();
    }

    // للحصول على القيم حسب خاصية معينة
    public function attributeValuesByAttribute($attribute_id)
    {
        return $this->attributeValues()->where('attribute_id', $attribute_id)->get();
    }

    // القيم اللي تولّد Variants (لازم تكون attributeValues محمّلة مسبقاً بـ with)
    public function variationValues()
    {
        return $this->attributeValues->filter(fn($value) => (bool) $value->attribute->is_variation);
    }

    // القيم اللي تُستخدم كإضافات (Modifiers)
    public function modifierValues()
    {
        return $this->attributeValues->filter(fn($value) => ! $value->attribute->is_variation);
    }
    // ✅ جديد: نسخة query-able من variationValues() - بترجع Builder مش
    // Collection، فبنقدر نستخدمها مع withExists/whereHas بدون ما نحمّل
    // كل الـ attributeValues مسبقاً
    public function variationAttributeValues()
    {
        return $this->attributeValues()->whereHas('attribute', function ($q) {
            $q->where('is_variation', true);
        });
    }

    public function wishLists()
    {
        return $this->hasMany(WishList::class, 'product_id');
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favoritedByUsers()->count(); //$product->favorites_count;

    }
    public function isFavoritedBy(User $user)
    {
        return $this->favoritedByUsers()->where('user_id', $user->id)->exists(); //$product->isFavoritedBy(auth()->user());

    }

    public function ScopeFilterApi($query, array $filters)
    {

        $query->when($filters['search_name'] ?? false, function ($query, $search) {
            return $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            });
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
{
    return $this->hasMany(ProductVariant::class);
}

  // ✅ Accessor نظيف بدون باراميترات، يحسب سعر المنتج الأساسي فقط
    public function getFinalPriceAttribute(): float
    {
        $base = (float) $this->netPrice;

        if ($this->isOnSale()) {
            $base -= ($base * $this->salePrice) / 100;
        }

        return (float) $base;
    }

    // ✅ دالة عادية (Method) للمنطق المعقد (لو فيه فيرنت)
    public function calculateFinalPrice($variant = null): float
    {
        // لو فيه فيرنت، نستخدم سعره، وإلا نستخدم السعر الأساسي للمنتج
        $base = $variant ? (float) $variant->price : (float) $this->netPrice;

        // نطبق الخصم (لو المنتج عليه عرض)
        if ($this->isOnSale()) {
            $base -= ($base * $this->salePrice) / 100;
        }

        return (float) $base;
    }
public function unit()
{
    return $this->belongsTo(Unit::class);
}
// جاهزة للعرض في أي صفحة: "500 مل" أو "غير محدود"
public function getQuantityWithUnitAttribute()
{
    if (is_null($this->quantity)) {
        return __('dashboard.Unlimited');
    }
    return $this->quantity . ($this->unit ? ' ' . $this->unit->name : '');
}


public function scopeLowStock($query)
{
    $threshold = Setting::getFloat('expired_quantities');

    return $query->where('track_quantity', true)
        ->whereNotNull('quantity')
        ->where('quantity', '<=', $threshold);
}

public function scopeOutOfStock($query)
{
    return $query->where('track_quantity', true)
        ->whereNotNull('quantity')
        ->where('quantity', '<=', 0);
}


public function scopeOnSale($query)
{
    $now = now();
    return $query->whereNotNull('salePrice')
        ->whereNotNull('salePrice_start')
        ->whereNotNull('salePrice_end')
        ->where('salePrice_start', '<=', $now)
        ->where('salePrice_end', '>=', $now);
}


}
