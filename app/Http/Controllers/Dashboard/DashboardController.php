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
        $count_traders = User::traders()->count();
        $count_drivers = User::Drivers()->count();
        $count_orders = Order::count();
        $today_Orders_Sales = $this->ordersSalesReport('today');
        $month_Orders_Sales = $this->ordersSalesReport('month');
        $year_Orders_Sales = $this->ordersSalesReport('year');
        $All_Orders_Sales = $this->ordersSalesReport(null);
        $orderstatus = Orderstatus::orderBy('id', 'asc')->get();

        $traders_pending   = User::pendingTraders()->count();
        $traders_approved  = User::approvedTraders()->count();
        $traders_suspended = User::suspendedTraders()->count();

        $drivers_pending  = User::pendingDrivers()->count();
        $drivers_approved = User::approvedDrivers()->count();

        $expiredThreshold = Setting::getFloat('expired_quantities');

        $low_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', $expiredThreshold)
            ->where('quantity', '>', 0)
            ->count();

        $out_of_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', 0)
            ->count();

        // ✅ أحدث الطلبات (مع إضافة التاجر)
        $recently_last_five_order = Order::select('id', 'order_number', 'user_id', 'trader_id', 'total_items', 'final_price', 'status_id', 'currency', 'read', 'created_at')->with([
            'status' => function ($query) {
                $query->select('id');
            },
            'user' => function ($query) {
                $query->select('id', 'name');
            },
            'trader' => function ($query) {
                $query->select('id', 'name');
            }
        ])->orderBy('id', 'DESC')->take(5)->get();

        // ✅ أحدث تقييمات التجار
        $recently_last_five_order_reviews = OrderReview::select(
            'id', 'client_id', 'rating', 'comment', 'order_id', 'created_at'
        )->with([
            'client' => function ($query) { $query->select('id', 'name'); },
            'order.trader' => function ($query) { $query->select('id', 'name'); }
        ])->whereHas('order.trader')->latest('id')->take(5)->get();

        // ✅ أحدث تقييمات السائقين
        $recently_last_five_driver_reviews = DriverReview::select(
            'id', 'client_id', 'driver_id', 'rating', 'comment', 'order_id', 'created_at'
        )->with([
            'client' => function ($query) { $query->select('id', 'name'); },
            'driver' => function ($query) { $query->select('id', 'name'); },
            'order' => function ($query) { $query->select('id', 'order_number'); }
        ])->latest('id')->take(5)->get();

        // ✅ دمج تقييمات التجار والسائقين وترتيبها بالأحدث
        $recently_all_reviews = $recently_last_five_order_reviews->concat($recently_last_five_driver_reviews)
            ->sortByDesc('created_at')
            ->take(5);

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

        // ✅ الإحصائيات المالية
        $completedOrders = Order::where('status_id', 5)->get();
        $financial_summary = [
            'total_sales'            => (float) $completedOrders->sum('final_price'),
            'total_deposit_collected' => (float) $completedOrders->sum('deposit_amount'),
            'total_remaining'         => (float) $completedOrders->sum('remaining_amount'),
            'total_commission_trader'  => (float) $completedOrders->sum('commission_trader'),
            'total_commission_driver'  => (float) $completedOrders->sum('commission_driver'),
            'total_slaughter'          => (float) $completedOrders->sum('slaughter_total'),
            'total_orders_completed'    => (int) $completedOrders->count(),
        ];

        $todayCompletedOrders = Order::where('status_id', 5)
            ->whereDate('delivered_at', today())->get();
        $today_financial = [
            'sales'             => (float) $todayCompletedOrders->sum('final_price'),
            'deposit_collected' => (float) $todayCompletedOrders->sum('deposit_amount'),
            'commission_trader' => (float) $todayCompletedOrders->sum('commission_trader'),
            'commission_driver' => (float) $todayCompletedOrders->sum('commission_driver'),
            'slaughter'         => (float) $todayCompletedOrders->sum('slaughter_total'),
            'orders_count'      => (int) $todayCompletedOrders->count(),
        ];

        $payment_methods_stats = Order::where('status_id', 5)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(final_price) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method')
            ->map(fn($row) => [
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ]);

        $data = [
            'count_clients' => $count_clients,
            'count_traders' => $count_traders,
            'count_drivers' => $count_drivers,
            'traders_pending'   => $traders_pending,
            'traders_approved'  => $traders_approved,
            'traders_suspended' => $traders_suspended,
            'drivers_pending'   => $drivers_pending,
            'drivers_approved'  => $drivers_approved,
            'count_orders' => $count_orders,
            'today_Orders_Sales' => $today_Orders_Sales,
            'month_Orders_Sales' => $month_Orders_Sales,
            'year_Orders_Sales' => $year_Orders_Sales,
            'All_Orders_Sales' => $All_Orders_Sales,
            'orderstatus' => $orderstatus,
            'recently_last_five_order' => $recently_last_five_order,
            'recently_all_reviews' => $recently_all_reviews,
            'expire_products_quantity' => $expire_products_quantity,
            'products_most_purchased_ten' => $topProducts,
            'low_stock_count' => $low_stock_count,
            'out_of_stock_count' => $out_of_stock_count,
            'expired_threshold' => $expiredThreshold,
            'financial_summary'     => $financial_summary,
            'today_financial'       => $today_financial,
            'payment_methods_stats' => $payment_methods_stats,
        ];

        return view('dashboard.homepage.index', $data);
    }

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
