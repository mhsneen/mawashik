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
        $setting = Setting::where('key', 'expired_quantities')->get();
        $count_clients = User::Clients()->count();
        $count_orders = Order::count();
        $today_Orders_Sales = $this->ordersSalesReport('today');
        $month_Orders_Sales = $this->ordersSalesReport('month');
        $year_Orders_Sales = $this->ordersSalesReport('year');
        $All_Orders_Sales = $this->ordersSalesReport(null);
        $orderstatus = Orderstatus::get();


        $recently_last_five_order = Order::select('id', 'user_id', 'total_items', 'final_price', 'status_id',  'currency', 'read', 'created_at')->with([
            'status' => function ($query) {
                $query->select('id');
            }

        ])->orderBy('id', 'DESC')->take(5)->get();

        $recently_last_five_order_reviews = OrderReview::select('id', 'client_id', 'rating', 'comment', 'order_id', 'created_at')->with([
            'client' => function ($query) {
                $query->select('id', 'name');
            }

        ])->take(5)->get();
        $expire_products_quantity = Product::select('id', 'image', 'quantity')->where('quantity', '<=', $setting[0]->plain_value)->take(10)->get();

        $topProducts = OrderItem::whereHas('order')
            ->with(['product:id,image']) // ✅ إحضار اسم وصورة المنتج
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



        ];
        return view('dashboard.homepage.index', $data);
    }

    public function ordersSalesReport($period = null)
    {
        $query = Order::query();

        // تجاهل الطلبات الملغاة
        $query->where('status_id', 4);



        // تحديد الفترة الزمنية
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
