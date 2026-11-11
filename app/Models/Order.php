<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_id',
        'driver_id',
        'delivery_type',
        'payment_method',
        'delivery_price',
        'total_items',
        'total_price',
        'discount_amount',
        'final_price',
        'status_id',
        'currency',
        'note',
        'latitude',
        'longitude',
        'read',
        'address',
        'added_tax',
        'transaction_id',
        'coupon_id',
        'coupon_code',
        'cancel_reason_id',
        'cancel_other_reason',
        'delivery_date',
        'delivery_time',
        'earned_points',
        'redeemed_points',
        'redeemed_points_value',
        'points_awarded_at',
        'prepared_at',
        'out_for_delivery_at',
        'delivered_at',
        'distance',
        'delivery_pin',
        'payment_collected',
        'driver_note',
        'payment_gateway_code',
        'payment_status',
        'payment_reference',
        'paid_at',
        'payment_gateway_response',
        'branch_id',
        'order_number',
        // === مواشي وسيط ===
        'pickup_type',
        'trader_id',
        'deposit_amount',
        'remaining_amount',
        'slaughter_total',
        'commission_trader',
        'commission_driver',
        'deposit_status',
        'deposit_method',
        'deposit_paid_at',
        'deposit_refunded_at',
        'driver_at_trader_at',
        'driver_picked_up_at',
        'delivery_stage',
        'trader_pin',
    ];


    protected $casts = [
        'total_price' => 'float',
        'final_price' => 'float',
        'discount_amount' => 'float',
        'added_tax' => 'float',
        'delivery_price' => 'float',
        'delivery_date' => 'date',
        // === مواشي وسيط ===
        'deposit_amount' => 'float',
        'remaining_amount' => 'float',
        'slaughter_total' => 'float',
        'commission_trader' => 'float',
        'commission_driver' => 'float',
        'deposit_paid_at' => 'datetime',
        'deposit_refunded_at' => 'datetime',
        'driver_at_trader_at' => 'datetime',
        'driver_picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // === مواشي وسيط — علاقة التاجر ===
    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    // قيمة المنتجات + الذبح بعد الخصم (حصة التاجر قبل العمولة) — بدون التوصيل والضريبة
    public function getTraderGrossAmountAttribute(): float
    {
        return max(0.0, (float) $this->total_price - (float) $this->discount_amount);
    }

    // المبلغ المستحق للتاجر = قيمة المنتجات بعد الخصم - عمولة المنصة من التاجر
    // (رسوم التوصيل تذهب للمندوب، لذلك لا تدخل في حصة التاجر)
    public function getTraderNetAmountAttribute(): float
    {
        return max(0.0, $this->trader_gross_amount - (float) $this->commission_trader);
    }

    // المبلغ الذي يجب على المندوب تحصيله نقداً من العميل:
    // الإجمالي ناقص العربون إن كان قد دُفع فعلاً مسبقاً. غير الكاش = 0.
    public function getCashToCollectAttribute(): float
    {
        if ($this->payment_method !== 'cash') {
            return 0.0;
        }

        $prepaidDeposit = $this->deposit_status === 'paid' ? (float) $this->deposit_amount : 0.0;

        return max(0.0, (float) $this->final_price - $prepaidDeposit);
    }

    // عربون فقط يُدفع إلكترونياً (الطلب نقدي والمتبقي عند الاستلام)
    public function getIsDepositOnlyOnlineAttribute(): bool
    {
        return $this->payment_method === 'cash'
            && $this->deposit_method === 'online'
            && (float) $this->deposit_amount > 0;
    }

    // المبلغ الذي تحصّله بوابة الدفع: العربون فقط أو الإجمالي
    public function getOnlineAmountAttribute(): float
    {
        return $this->is_deposit_only_online
            ? (float) $this->deposit_amount
            : (float) $this->final_price;
    }

    // العربون مدفوع أو الطلب مدفوع بالكامل (أي وصل التاجر مبلغ مضمون)
    public function hasSecuredPayment(): bool
    {
        return $this->payment_status === 'paid' || $this->deposit_status === 'paid';
    }

    // هل العربون قابل للاسترداد؟ (قبل بدء التجهيز فقط — حسب الإعداد deposit_refund_before)
    public function isDepositRefundable(): bool
    {
        if ($this->deposit_status !== 'paid') {
            return false;
        }
        $refundStage = Setting::where('key', 'deposit_refund_before')->value('plain_value') ?? 'preparing';
        return match ($refundStage) {
            'anytime'         => in_array($this->status_id, [1, 2, 3, 4, 7]),
            'preparing'       => in_array($this->status_id, [1, 7]),
            'never'           => false,
            default           => in_array($this->status_id, [1, 7]),
        };
    }

    // العنوان المحفوظ اللي اختاره العميل وقت الطلب (Snapshot النصي محفوظ
    // بأعمدة address/latitude/longitude بالطلب نفسه بغض النظر عن هذي العلاقة)
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function getPriceAfterDiscountAttribute()
    {
        return $this->total_price - $this->discount_amount;
    }

    // ✅ قيمة مبلغ الضريبة المضافة
    public function getTaxAmountAttribute()
    {
        $tax = $this->added_tax ?? 0;
        return $this->price_after_discount * ($tax / 100);
    }

    // ✅ السعر النهائي شامل الضريبة
    public function getPriceAfterTaxAttribute()
    {
        return $this->price_after_discount + $this->tax_amount;
    }

    // العلاقة مع المستخدم صاحب الطلب
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // العلاقة مع السائق المعيّن للطلب
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }



    // العلاقة مع حالة الطلب
    public function status()
    {
        return $this->belongsTo(Orderstatus::class, 'status_id');
    }

    // العلاقة مع المنتجات في الطلب
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // إذا كان هناك كوبون
    public function coupon()
    {
        return $this->belongsTo(DiscountCode::class, 'coupon_id');
    }




    public function scopeRead($query)
    {
        return $query->where('read', '0');
    }
    public function review()
    {
        return $this->hasOne(OrderReview::class);
    }

    // تقييم العميل للسائق على هذا الطلب
    public function driverReview()
    {
        return $this->hasOne(DriverReview::class);
    }

    public function ScopeFilterDashbordAdmin($query, array $filters)
    {

        $query->when($filters['search_client_name'] ?? false, function ($query, $search) {
            return   $query->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        });

        $query->when($filters['search_client_mobile'] ?? false, function ($query, $search) {
            return   $query->whereHas('user', function ($query) use ($search) {
                $query->where('mobile', $search);
            });
        });


        $query->when($filters['search_order_number'] ?? false, function ($query, $search) {

                            $query->where('order_number', 'LIKE', '%' . $search . '%');

        });
        $query->when($filters['search_status_id'] ?? false, function ($query, $search) {
            return  $query->where('status_id', $search);
        });

  $query->when($filters['search_trader_id'] ?? false, function ($query, $search) {
            return  $query->where('trader_id', $search);
        });


        $query->when($filters['search_date_from'] ?? false, function ($query, $searchprice) {

            return  $query->whereBetween('created_at', [$searchprice, request('search_date_to')]);
        });
    }


    public static function unreadCount()
    {

        $query = self::Read(); // سكوب الطلبات غير المقروءة
        return $query->count();
    }


    public function cancelReason()
    {
        return $this->belongsTo(OrderCancelReason::class, 'cancel_reason_id');
    }


    public function scopeStatusNew($query)
    {
        return $query->where('status_id', 1);
    }




// 1 = طلب جديد           (new)
// 2 = قيد التجهيز          (preparing)
// 3 = تم التجهيز           (prepared / ready)
// 4 = قيد التوصيل          (out_for_delivery)  ← جديدة، بس لنوع delivery
// 5 = تم التسليم           (delivered)          ← الحالة النهائية الناجحة
// 6 = ملغي                (cancelled)
// 7 = بإنتظار الدفع                 (Payment pending)



public function canChangeStatusTo($newStatus)
{
    $allowedTransitions = [
        1 => [2, 6], // العميل يمكنه الإلغاء فقط إذا كان الطلب "جديد"
        2 => [3],    // ❌ تم إزالة 6، لا يمكن الإلغاء بعد بدء التجهيز
        3 => $this->delivery_type === 'delivery' ? [4] : [5],
        4 => [5],
        5 => [],
        6 => [],
        7 => [1, 6], // يمكن الإلغاء إذا كان بانتظار الدفع
    ];

    return in_array($newStatus, $allowedTransitions[$this->status_id] ?? []);
}

public function getAllowedStatuses()
{
    $allowedTransitions = [
        1 => [2, 6],
        2 => [3],    // ❌ تم إزالة 6
        3 => $this->delivery_type === 'delivery' ? [4] : [5],
        4 => [5],
        5 => [],
        6 => [],
        7 => [1, 6],
    ];

    return $allowedTransitions[$this->status_id] ?? [];
}

    public function ScopeFilterDashbordSales($query, array $filters)
    {


        $query->when($filters['search_date_from'] ?? false, function ($query, $searchprice) {

            return  $query->whereBetween('created_at', [$searchprice, request('search_date_to')]);
        });
    }

    // Accessor to get combined datetime
    public function getDeliveryAtAttribute()
    {
        if (!$this->delivery_date || !$this->delivery_time) {
            return null;
        }

        return \Carbon\Carbon::parse($this->delivery_date->format('Y-m-d') . ' ' . $this->delivery_time->format('H:i:s'));
    }

    // Mutator to set delivery_date and delivery_time from a single datetime
    public function setDeliveryAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['delivery_date'] = null;
            $this->attributes['delivery_time'] = null;
            return;
        }

        $dateTime = \Carbon\Carbon::parse($value);

        $this->attributes['delivery_date'] = $dateTime->format('Y-m-d');
        $this->attributes['delivery_time'] = $dateTime->format('H:i:s');
    }

  public function scopeStatusCurrent($query)
{
    return $query->whereIn('status_id', [1, 2, 3, 4]); // ضفت 4 (قيد التوصيل)
}

   public function scopeStatusEnd($query)
{
    return $query->where('status_id', 5); // كانت 4
}

public function scopeStatusCancelled($query)
{
    return $query->where('status_id', 6);
}

    // طلبيات السائق: المعيّنة له حالياً (قيد التوصيل)
    public function scopeAssignedToDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    // طلبيات مكتملة للسائق
    public function scopeDriverCompleted($query, int $driverId)
    {
        return $query->where('driver_id', $driverId)->where('status_id', 5);
    }

    // توليد كود التسليم (4 أرقام)
    public static function generateDeliveryPin(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }


    protected static function booted()
{
    static::creating(function ($order) {
        $order->order_number = self::generateOrderNumber();
    });
}

public static function generateOrderNumber(): string
{
    $year = now()->year;

    return DB::transaction(function () use ($year) {
        // آخر رقم لنفس السنة، مع قفل الصف لمنع التعارض عند طلبات متزامنة
        $lastOrder = self::withTrashed()
            ->where('order_number', 'like', "ORD-{$year}-%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder->order_number, -3);
            $nextNumber = $lastSequence + 1;
        }

        return sprintf('ORD-%d-%03d', $year, $nextNumber);
    });
}

public function getPaymentStatusLabelAttribute()
{
    return match ($this->payment_status) {
        'pending'  => __('dashboard.Payment_Status_Pending'),
        'paid'     => __('dashboard.Payment_Status_Paid'),
        'failed'   => __('dashboard.Payment_Status_Failed'),
        'refunded' => __('dashboard.Payment_Status_Refunded'),
        'cancelled'=> __('dashboard.Payment_Status_Cancelled'),
        default    => $this->payment_status,
    };
}



public function getPaymentMethodLabelAttribute()
{
    return match ($this->payment_method) {
        'cash'   => __('dashboard.Cash_Payment'),
        'online' => __('dashboard.Online_Payment'),
        'wallet' => __('dashboard.Wallet_Payment'),
        default  => $this->payment_method,
    };
}



public function getDeliveryTypeLabelAttribute()
{
    return $this->delivery_type === 'pickup'
        ? __('dashboard.Pickup_From_Store')
        : __('dashboard.Delivery_To_Address');
}

public function driverOffers()
{
    return $this->hasMany(OrderDriverOffer::class);
}


}

