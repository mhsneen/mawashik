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
    protected $fillable = ['netPrice', 'salePrice', 'salePrice_start', 'salePrice_end', 'is_active', 'new_arrivals', 'brand_id', 'sku', 'quantity', 'category_id', 'image', 'calories'];
    protected $date = ['salePrice_start', 'salePrice_end', 'deleted_at'];
    protected $translatedAttributes = ['name', 'slug', 'details', 'description'];
    protected $hidden = ['translations', 'image'];
    const FOLDER = 'products';
    protected $appends = ['image_url', 'final_price', 'is_on_sale', 'base_image_url'];

    protected $casts = [
        'netPrice' => 'float',   // أو 'decimal:2' إذا تريد دقة عشريتين
        'salePrice' => 'float',
        'final_price' => 'float',
    ];

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
        $query->when($filters['search_brand_id'] ?? false, function ($query, $search) {
            return  $query->where('brand_id', $search);
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

    public function hasStock(int $quantity = 1): bool
    {
        return $this->quantity >= $quantity;
    }

    public function inStock(): bool
    {
        return $this->hasStock();
    }

    public function outOfStock(): bool
    {
        return ! $this->hasStock();
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

public function getFinalPriceAttribute($variant = null)
{
    $base = $variant?->price ?? $this->netPrice;
    if ($this->isOnSale()) {
        $base -= ($base * $this->salePrice) / 100;
    }
    return (float) $base;
}
}
