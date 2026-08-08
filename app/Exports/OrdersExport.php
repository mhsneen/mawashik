<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Auth;

class OrdersExport implements FromView, WithEvents
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
        $user = Auth::user();


       $query = Order::FilterDashbordAdmin([
    'search_order_number' => $request->search_order_number,
    'search_client_name' => $request->search_client_name,
    'search_client_mobile' =>  $request->search_client_mobile,
    'search_status_id' =>   $request->search_status_id,
    'search_date_from' =>    $request->search_date_from,
    'search_date_to' =>    $request->search_date_to
])->with('user:id,name', 'status:id', 'coupon:code,discount_percentage')
    ->select(
        'id', 'user_id', 'total_items', 'status_id', 'currency', 'read', 'created_at',
        'total_price', 'discount_amount', 'coupon_id', 'added_tax', 'final_price', 'order_number',
        'payment_status', 'delivery_type', 'delivery_price', 'payment_method'
    )->latest('id');

$items = $query->get();
        return view('dashboard.orders.excel.index', [
            'items' => $items
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
