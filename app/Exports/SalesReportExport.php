<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReportExport implements FromView, WithEvents
{
    protected $request;
    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */

    public function view(): View
    {
        $request = $this->request;

        $query = Order::FilterDashbordSales(request(['search_date_from', 'search_date_to']))
            ->where('status_id', 4); // استثناء الملغاة

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
            ->get();
        return view('dashboard.reports.excel.sales', [
            'orders' => $orders,
            'summary' => $summary
        ]);
    }
    // 👇 هذه الدالة هي المفتاح لتفعيل RTL
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
