<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'image',
        'is_active',
        'device_token',
        'desktop_token',
        'branch_id',
        'is_blocked',
    ];
    protected $appends = ['image_url', 'base_image_url'];
    const FOLDER = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/thumbnail/' . $this->image) : asset('uploads/users/avatar.png');
    }

    public function getBaseImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . self::FOLDER . '/' . $this->image) : asset('uploads/users/avatar.png');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeClients($query)
    {
        return $query->role('client', 'web');
    }


    public function scopeDrivers($query)
    {
        return $query->role('driver', 'web');
    }

        public function ScopeFilterDashbordDrivers($query, array $filters)
    {
        $query->when($filters['search_name'] ?? false, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        });

        $query->when($filters['search_mobile'] ?? false, function ($query, $search) {
            return $query->where('mobile', 'LIKE', '%' . $search . '%');
        });

        // ✅ فلتر حالة الفعالية
        $query->when($filters['search_active'] ?? false, function ($query, $search) {
            if ($search === '1') {
                return  $query->where('is_active', '1');
            } else if ($search === '2') {
                return  $query->where('is_active', '0');
            }
        });

        // ✅ فلتر حالة الحظر
        $query->when($filters['search_is_blocked'] ?? false, function ($query, $search) {
            return $query->where('is_blocked', $search);
        });
    }

    public function scopeEmployees($query)
    {
        return $query->role('employee', 'admin');
    }
        public function ScopeFilterDashbordClients($query, array $filters)
    {
        $query->when($filters['search_name'] ?? false, function ($query, $search) {
            return  $query->where('name', 'LIKE', '%' . $search . '%');
        });

        $query->when($filters['search_mobile'] ?? false, function ($query, $search) {
            return  $query->where('mobile', 'LIKE', '%' . $search . '%');
        });

        // ✅ فلتر حالة الفعالية
        $query->when($filters['search_active'] ?? false, function ($query, $search) {
            if ($search === '1') {
                return  $query->where('is_active', '1');
            } else if ($search === '2') {
                return  $query->where('is_active', '0');
            }
        });

        // ✅ فلتر حالة الحظر
        $query->when($filters['search_is_blocked'] ?? false, function ($query, $search) {
            return $query->where('is_blocked', $search);
        });
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // طلبيات سائق (الطلبات المعيّنة له)
    public function driverOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    // تقييمات السائق من العملاء
    public function driverReviews()
    {
        return $this->hasMany(DriverReview::class, 'driver_id');
    }

    // متوسط تقييم السائق (بيرجع null لو ماله تقييمات لسا)
    public function getAverageRatingAttribute()
    {
        return $this->driverReviews()->avg('rating');
    }

    // عدد تقييمات السائق
    public function getReviewsCountAttribute()
    {
        return $this->driverReviews()->count();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    public function favoriteProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'wish_lists',
            'user_id',        // current user (customer)
            'product_id'   // favorited product
        )->withTimestamps();
    }

    /**
     * Users (customers) who have favorited this product
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'wish_lists',
            'product_id',  // this product
            'user_id'         // users who favorited
        )->withTimestamps();
    }

    /**
     * Check if user has favorited this product
     */
   public function isFavorited(int $productId)
    {
        return $this->favoriteProducts()
            ->where('product_id', $productId)
            ->exists();
    }
    public function wishLists()
    {
        return $this->hasMany(WishList::class, 'user_id');
    }

    protected function email(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ?? ''
    );
}

public function loyaltyPointTransactions()
{
    return $this->hasMany(LoyaltyPointTransaction::class);
}
}
