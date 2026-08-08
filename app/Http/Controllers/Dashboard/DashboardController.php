<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Product, User, Order, Orderstatus, OrderReview, Setting, OrderItem};

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

        // ✅ عداد المنتجات اللي شارفت على الانتهاء (فقط اللي بتتبع المخزون)
        $low_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', $expiredThreshold)
            ->where('quantity', '>', 0)
            ->count();

        // ✅ عداد المنتجات اللي نفذت الكمية
        $out_of_stock_count = Product::where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', 0)
            ->count();

        $recently_last_five_order = Order::select('id', 'order_number','user_id', 'total_items', 'final_price', 'status_id', 'currency', 'read', 'created_at')->with([
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
])->take(5)->get();

        // ✅ المنتجات اللي شارفت على الانتهاء (فقط اللي بتتبع المخزون وعندها كمية محددة)
        $expire_products_quantity = Product::select('id',  'image', 'quantity', 'track_quantity')
            ->where('track_quantity', true)
            ->whereNotNull('quantity')
            ->where('quantity', '<=', $expiredThreshold)
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
            'expire_products_quantity' => $expire_products_quantity,
            'products_most_purchased_ten' => $topProducts,
            // ← إحصائيات جديدة
            'low_stock_count' => $low_stock_count,
            'out_of_stock_count' => $out_of_stock_count,
            'expired_threshold' => $expiredThreshold,
        ];
        return view('dashboard.homepage.index', $data);
    }

    public function ordersSalesReport($period = null)
    {
        $query = Order::query();
        $query->where('status_id', 4);

        if ($period === 'today') {
            $query->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()]);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $orders = $query->get();

        return [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('final_price'),
        ];
    }
}
