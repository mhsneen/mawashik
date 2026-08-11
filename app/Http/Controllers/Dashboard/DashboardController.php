<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Product, User, Order, Orderstatus, OrderReview, DriverReview, Setting, OrderItem};

class DashboardController extends Controller
{
    public function index()
    {
        $count_clients = User::Clients()->count();
        $count_orders = Order::count();
        $today_Orders_Sales = $this->ordersSalesReport('today');
        $month_Orders_Sales = $this->ordersSalesReport('month');
        $year_Orders_Sales = $this->ordersSalesReport('year');
        $All_Orders_Sales = $this->ordersSalesReport(null);
        $orderstatus = Orderstatus::orderBy('id', 'asc')->get();

        // ✅ جلب حد التنبيه من الإعدادات (قيمة موحدة)
        $expiredThreshold = Setting::getFloat('expired_quantities');

        // ✅ عداد المنتجات اللي شارفت على الانتهاء (فقط اللي بتتبع المخزون، وما زالت متوفرة > 0)
        $low_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', $expiredThreshold)
            ->where('quantity', '>', 0)
            ->count();

        // ✅ عداد المنتجات اللي نفذت الكمية تماماً
        $out_of_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', 0)
            ->count();

        $recently_last_five_order = Order::select('id', 'order_number', 'user_id', 'total_items', 'final_price', 'status_id', 'currency', 'read', 'created_at')->with([
            'status' => function ($query) {
                $query->select('id');
            }
        ])->orderBy('id', 'DESC')->take(5)->get();

        $recently_last_five_order_reviews = OrderReview::select(
            'id',
            'client_id',
            'rating',
            'comment',
            'order_id',
            'created_at'
        )->with([
            'client' => function ($query) {
                $query->select('id', 'name');
            },
            'order' => function ($query) {
                $query->select('id', 'order_number');
            }
        ])->latest('id')->take(5)->get();

        // ✅ أحدث تقييمات العملاء للسائقين
        $recently_last_five_driver_reviews = DriverReview::select(
            'id',
            'client_id',
            'driver_id',
            'rating',
            'comment',
            'order_id',
            'created_at'
        )->with([
            'client' => function ($query) {
                $query->select('id', 'name');
            },
            'driver' => function ($query) {
                $query->select('id', 'name');
            },
            'order' => function ($query) {
                $query->select('id', 'order_number');
            }
        ])->latest('id')->take(5)->get();

        // ✅ المنتجات اللي شارفت على الانتهاء فعلاً (بتتبع المخزون + عندها كمية محددة + ما زالت > 0)
        // ملاحظة: تم استبعاد المنتجات المنتهية تماماً (quantity <= 0) من هذه القائمة
        // لأنها مفصولة في بطاقة "المنتجات المنتهية من المخزون" الخاصة بها.
        $expire_products_quantity = Product::select('id', 'image', 'quantity', 'track_quantity')
            ->where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', $expiredThreshold)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get();

        $topProducts = OrderItem::whereHas('order')
            ->with(['product:id,image'])
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        $data = [
            'count_clients' => $count_clients,
            'count_orders' => $count_orders,
            'today_Orders_Sales' => $today_Orders_Sales,
            'month_Orders_Sales' => $month_Orders_Sales,
            'year_Orders_Sales' => $year_Orders_Sales,
            'All_Orders_Sales' => $All_Orders_Sales,
            'orderstatus' => $orderstatus,
            'recently_last_five_order' => $recently_last_five_order,
            'recently_last_five_order_reviews' => $recently_last_five_order_reviews,
            'recently_last_five_driver_reviews' => $recently_last_five_driver_reviews,
            'expire_products_quantity' => $expire_products_quantity,
            'products_most_purchased_ten' => $topProducts,
            // ← إحصائيات المخزون
            'low_stock_count' => $low_stock_count,
            'out_of_stock_count' => $out_of_stock_count,
            'expired_threshold' => $expiredThreshold,
        ];
        return view('dashboard.homepage.index', $data);
    }

    /**
     * ✅ تصحيح جوهري: التقرير كان يعتمد على status_id = 4 (قيد التوصيل) وهي ليست
     * حالة نهائية للمبيعة، بينما status_id = 5 (تم التسليم) هي الحالة الصحيحة التي
     * تُعتبر عندها المبيعة محققة فعلياً (نفس المنطق المستخدم في ReportController).
     * كما تم اعتماد delivered_at بدل created_at كأساس للفترة الزمنية، لأن الطلب
     * قد يُنشأ في يوم ويُسلَّم في يوم آخر، والمبيعة الفعلية تُحتسب وقت التسليم.
     */
    public function ordersSalesReport($period = null)
    {
        $query = Order::query();
        $query->where('status_id', 5);

        if ($period === 'today') {
            $query->whereDate('delivered_at', Carbon::today());
        } elseif ($period === 'month') {
            $query->whereMonth('delivered_at', Carbon::now()->month)
                ->whereYear('delivered_at', Carbon::now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('delivered_at', Carbon::now()->year);
        }

        $orders = $query->get();

        return [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('final_price'),
        ];
    }
}
