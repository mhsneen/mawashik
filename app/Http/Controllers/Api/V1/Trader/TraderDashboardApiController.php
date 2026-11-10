<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, Wallet};
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * TraderDashboardApiController — الصفحة الرئيسية وشاشة الأرباح للتاجر.
 *
 *  - summary:  ملخص المبيعات + الطلبات + المنتجات + المخزون + المحفظة (الصفحة الرئيسية).
 *  - earnings: إجمالي المبيعات والعمولات وصافي المستحقات والطلبات المكتملة (شاشة الأرباح والعمولات).
 *
 * تعريف المبيعات: قيمة المنتجات + الذبح بعد الخصم (بدون التوصيل والضريبة)، من الطلبات المكتملة (status 5).
 * صافي المستحقات = المبيعات - عمولة المنصة.
 */
class TraderDashboardApiController extends Controller
{
    use ApiTrait;

    public function summary(Request $request)
    {
        try {
            $trader = $request->user();

            $orders = Order::where('trader_id', $trader->id);

            $byStatus = (clone $orders)
                ->selectRaw('status_id, COUNT(*) as c')
                ->groupBy('status_id')
                ->pluck('c', 'status_id');

            $completed = (clone $orders)->where('status_id', 5);

            $products = Product::where('trader_id', $trader->id);

            $wallet = $trader->wallet ?: Wallet::firstOrCreate(
                ['user_id' => $trader->id],
                ['balance' => 0, 'rolename' => 'trader']
            );

            return $this->returnData('data', [
                'sales' => [
                    'today' => $this->aggregate((clone $completed)->whereDate('delivered_at', today())),
                    'month' => $this->aggregate(
                        (clone $completed)
                            ->whereMonth('delivered_at', now()->month)
                            ->whereYear('delivered_at', now()->year)
                    ),
                    'total' => $this->aggregate(clone $completed),
                ],
                'orders' => [
                    'new'       => (int) ($byStatus[1] ?? 0),
                    'preparing' => (int) ($byStatus[2] ?? 0),
                    'ready'     => (int) ($byStatus[3] ?? 0),
                    'out_for_delivery' => (int) ($byStatus[4] ?? 0),
                    'completed' => (int) ($byStatus[5] ?? 0),
                    'cancelled' => (int) ($byStatus[6] ?? 0),
                ],
                'products' => [
                    'total'        => (clone $products)->count(),
                    'active'       => (clone $products)->where('is_active', 1)->count(),
                    'out_of_stock' => (clone $products)->where('track_quantity', true)
                        ->whereNotNull('quantity')->where('quantity', '<=', 0)->count(),
                ],
                'wallet' => [
                    'balance'  => (float) $wallet->balance,
                    'currency' => 'SAR',
                ],
                'commission_rate' => $trader->commission_rate !== null ? (float) $trader->commission_rate : null,
            ], __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Trader summary: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function earnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $trader = $request->user();

            $query = Order::where('trader_id', $trader->id)->where('status_id', 5);

            if ($request->filled('date_from')) {
                $query->whereDate('delivered_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('delivered_at', '<=', $request->date_to);
            }

            $totals = $this->aggregate(clone $query);

            $orders = $query->orderByDesc('delivered_at')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $items = $orders->getCollection()->map(fn(Order $o) => [
                'id'              => $o->id,
                'order_number'    => $o->order_number,
                'delivered_at'    => $o->delivered_at?->format('Y-m-d H:i'),
                'sales'           => (float) $o->trader_gross_amount,
                'commission'      => (float) $o->commission_trader,
                'net'             => (float) $o->trader_net_amount,
                'deposit_amount'  => (float) $o->deposit_amount,
                'payment_method'  => $o->payment_method,
            ]);

            return $this->returnData('data', [
                'totals' => $totals,
                'items'  => $items,
                'meta'   => [
                    'current_page' => $orders->currentPage(),
                    'total_pages'  => $orders->lastPage(),
                    'total_items'  => $orders->total(),
                ],
            ], __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Trader earnings: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * @return array{sales: float, commission: float, net: float, orders: int}
     */
    private function aggregate($query): array
    {
        $row = $query->selectRaw(
            'COALESCE(SUM(GREATEST(total_price - discount_amount, 0)), 0) as sales, '
            . 'COALESCE(SUM(commission_trader), 0) as commission, '
            . 'COUNT(*) as orders'
        )->first();

        $sales      = (float) ($row->sales ?? 0);
        $commission = (float) ($row->commission ?? 0);

        return [
            'sales'      => round($sales, 2),
            'commission' => round($commission, 2),
            'net'        => round(max(0, $sales - $commission), 2),
            'orders'     => (int) ($row->orders ?? 0),
        ];
    }
}
