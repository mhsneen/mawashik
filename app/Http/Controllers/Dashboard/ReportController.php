<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, OrderItem};
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:The_Sales')
            ->only(['salesReport', 'export_sales_excel']);

        $this->middleware('permission:Products_purchased')
            ->only(['product_purchased']);
    }


    public function salesReport(Request $request)
    {



        // نبدأ بكويري الفلترة بدون paginate
        $query = Order::FilterDashbordSales(request(['search_date_from', 'search_date_to']))
            ->where('status_id', 5); // فقط الطلبات المسلمة فعليًا



        // الإحصائيات قبل عملية الـ paginate
        $summary = [
            'total_orders' => (clone $query)->count(),
            'total_sales' => (clone $query)->sum('final_price'),

        ];

        // الآن نجلب البيانات للعرض مع العلاقات + Paginate
        $orders = $query->with([
            'user:id,name',
            'status:id'
        ])
            ->latest('id')
            ->paginate(PAGANATION_COUNT)
            ->withQueryString();
        $request_search = $request->all();
        return view('dashboard.reports.sales', compact('orders', 'summary', 'request_search'));
    }

    public function export_sales_excel(Request $request)
    {

        return Excel::download(new SalesReportExport($request), 'sales.xlsx');
    }



function product_purchased(Request $request)
{
    $items = OrderItem::query()
        ->select(
            'order_items.id',
            'order_items.order_id',
            'order_items.product_id',
            'order_items.unit_price',
            'order_items.quantity',
            'order_items.total_price',
            'order_items.created_at'
        )
        ->whereHas('order', function ($q) {
            $q->where('status_id', 5);
        })
        ->FilterDashbordReport(
            request(['search_date_from', 'search_date_to'])
        )
        ->with([
            'order:id,order_number',
            'product:id,image,category_id',
            'product.category:id'
        ])
        ->latest('order_items.id')
        ->paginate(PAGANATION_COUNT)
        ->withQueryString();

    return view('dashboard.reports.product_purchased', compact('items'));
}


}
