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
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
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
        'id_number',
        'whatsapp_number',
        'image',
        'banner_image',
        'is_active',
        'is_available',
        'device_token',
        'desktop_token',
        'branch_id',
        'is_blocked',
        // === مواشي وسيط — الموافقة على الشروط ===
        'terms_accepted',
        // === مواشي وسيط — التاجر ===
        'trader_status',
        'commission_rate',
        'commercial_register',
        'store_lat',
        'store_lng',
        'store_address',
        'approved_at',
        'rejected_at',
        'admin_status_note',
        // === مواشي وسيط — المندوب ===
        'driver_status',
        'driver_commission_rate',
        'id_image',
        'license_image',
        'driver_approved_at',
        'driver_rejected_at',
        'driver_status_note',
        // === الجغرافيا ===
        'country_id',
        'city_id',
        'area_id',
        'neighborhood_id',
        // === الإشعارات ===
        'notifications_enabled',
    ];
    protected $appends = ['image_url', 'base_image_url', 'banner_url', 'whatsapp_link'];
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
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'driver_approved_at' => 'datetime',
            'driver_rejected_at' => 'datetime',
            // === رقم الجوال بصيغة E.164 (يطبّق country SA تلقائياً عند الإدخال) ===
            'mobile' => E164PhoneNumberCast::class . ':SA',
            'whatsapp_number' => E164PhoneNumberCast::class . ':SA',
            // === الحالات ===
            'is_available'         => 'boolean',
            'notifications_enabled' => 'boolean', // ✅ جديد — يرجع true/false وليس 1/0
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

    /**
     * رابط صورة البانر للمتجر (لو غير موجودة يرجع null بدل avatar).
     */
    public function getBannerUrlAttribute()
    {
        return $this->banner_image
            ? asset('uploads/' . self::FOLDER . '/' . $this->banner_image)
            : null;
    }

    /**
     * رقم واتساب فعلي (لو لم يُحدد يستخدم رقم الجوال).
     * يرجع بدون '+' وبدون مسافات لتجهيز رابط wa.me.
     *
     * ملاحظة: نأخذه من attributes مباشرة لتجنب التعارض مع cast الـ E164PhoneNumberCast.
     */
    public function getWhatsappNumberAttribute()
    {
        $number = $this->attributes['whatsapp_number'] ?? null;
        if (!$number) {
            $number = $this->attributes['mobile'] ?? null;
        }
        if (!$number) {
            return null;
        }
        return preg_replace('/[^0-9]/', '', (string) $number);
    }

    /**
     * رابط واتساب جاهز للضغط.
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        $number = $this->whatsapp_number;
        return $number ? "https://wa.me/{$number}" : null;
    }

    /**
     * رقم واتساب بصيغة E.164 مع + (للعرض).
     */
    public function getWhatsappE164Attribute(): ?string
    {
        $number = $this->whatsapp_number;
        return $number ? '+' . $number : null;
    }

    /**
     * آخر موقع معروف للمندوب (Live Tracking).
     */
    public function driverLocation()
    {
        return $this->hasOne(\App\Models\DriverLocation::class, 'driver_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeClients($query)
    {
        return $query->role('client', 'web');
    }

    // === مواشي وسيط ===
    public function scopeTraders($query)
    {
        return $query->role('trader', 'web');
    }

    public function scopePendingTraders($query)
    {
        return $query->role('trader', 'web')->where('trader_status', 'pending');
    }

    public function scopeApprovedTraders($query)
    {
        return $query->role('trader', 'web')->where('trader_status', 'approved');
    }

    public function scopeSuspendedTraders($query)
    {
        return $query->role('trader', 'web')->where('trader_status', 'suspended');
    }

    public function scopePendingDrivers($query)
    {
        return $query->role('driver', 'web')->where('driver_status', 'pending');
    }

    public function scopeApprovedDrivers($query)
    {
        return $query->role('driver', 'web')->where('driver_status', 'approved');
    }

    // هل المستخدم تاجر معتمد؟ (يستخدم في middleware)
    public function isApprovedTrader(): bool
    {
        return $this->hasRole('trader', 'web') && $this->trader_status === 'approved';
    }

    public function isApprovedDriver(): bool
    {
        return $this->hasRole('driver', 'web') && $this->driver_status === 'approved';
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

    /**
     * تقييمات التاجر (من تقييمات الطلبات المسلّمة).
     */
    public function traderReviews()
    {
        return $this->hasMany(OrderReview::class, 'trader_id');
    }

    /**
     * متوسط تقييم التاجر وعدد التقييمات الظاهرة (بدون المخفية).
     * يُخزَّن مؤقتاً على الطلب الواحد كي لا يتكرر الاستعلام لنفس التاجر في القوائم.
     *
     * @return array{average: float, count: int}
     */
    public static function traderRatingStats(int $traderId): array
    {
        static $memo = [];

        if (!isset($memo[$traderId])) {
            $row = OrderReview::where('trader_id', $traderId)
                ->visible()
                ->selectRaw('COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as c')
                ->first();

            $memo[$traderId] = [
                'average' => round((float) ($row->avg_rating ?? 0), 1),
                'count'   => (int) ($row->c ?? 0),
            ];
        }

        return $memo[$traderId];
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

// === مواشي وسيط — علاقات التاجر ===

/**
 * منتجات التاجر (فقط إذا كان تاجر).
 */
public function traderProducts()
{
    return $this->hasMany(Product::class, 'trader_id');
}

/**
 * طلبات التاجر (الطلبات التي تحتوي منتجاته).
 */
public function traderOrders()
{
    return $this->hasMany(Order::class, 'trader_id');
}

/**
 * سجل العمولات الخاصة بالتاجر.
 */
public function commissions()
{
    return $this->hasMany(Commission::class, 'user_id');
}

/**
 * العمولة الفعّالة الحالية للتاجر أو المندوب.
 */
public function currentCommission(string $role): ?Commission
{
    return $this->commissions()
        ->where('role', $role)
        ->where('is_active', true)
        ->where('effective_from', '<=', now()->toDateString())
        ->where(function ($q) {
            $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString());
        })
        ->orderByDesc('effective_from')
        ->first();
}

// === علاقات الجغرافيا ===
public function country()
{
    return $this->belongsTo(Country::class);
}

public function city()
{
    return $this->belongsTo(City::class);
}

public function area()
{
    return $this->belongsTo(Area::class);
}

public function neighborhood()
{
    return $this->belongsTo(Neighborhood::class);
}
}
