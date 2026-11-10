@extends('layouts.admin_dashboard')

@section('title')
    {{ __('dashboard.success_login') }}
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h2 class="main-content-label tx-24 mg-b-1 mg-b-lg-1">{{ __('dashboard.success_login') }} </h2>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div style="text-align: center">
                <label class="tx-15">{{ __('dashboard.Number_of_clients') }} </label>
                <h5>{{ $count_clients }} </h5>
            </div>
            <div style="text-align: center">
                <label class="tx-15">{{ __('dashboard.Number_of_traders') }} </label>
                <h5>{{ $count_traders }}
                    <small class="d-block tx-11 text-muted">
                        <span class="text-warning">{{ __('dashboard.Pending') }}: {{ $traders_pending }}</span> •
                        <span class="text-success">{{ __('dashboard.Approved') }}: {{ $traders_approved }}</span> •
                        <span class="text-danger">{{ __('dashboard.Suspended') }}: {{ $traders_suspended }}</span>
                    </small>
                </h5>
            </div>
            <div style="text-align: center">
                <label class="tx-15">{{ __('dashboard.Number_of_drivers') }} </label>
                <h5>{{ $count_drivers }}
                    <small class="d-block tx-11 text-muted">
                        <span class="text-warning">{{ __('dashboard.Pending') }}: {{ $drivers_pending }}</span> •
                        <span class="text-success">{{ __('dashboard.Approved') }}: {{ $drivers_approved }}</span>
                    </small>
                </h5>
            </div>
            <div style="text-align: center">
                <label class="tx-15">{{ __('dashboard.number_of_orders') }} </label>
                <h5>{{ $count_orders }} </h5>
            </div>
        </div>
    </div>
@endsection

@section('content')
 @can('Homepage')
    <div class="row row-sm">
        {{-- بطاقات المبيعات --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TODAY_ORDERS') }}</h6>
                    <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $today_Orders_Sales['total_orders'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TODAY_SALES') }}</h6>
                    <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($today_Orders_Sales['total_sales'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.MONTH_ORDERS') }}</h6>
                    <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $month_Orders_Sales['total_orders'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.MONTH_SALES') }}</h6>
                    <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($month_Orders_Sales['total_sales'], 2) }}</h4>
                </div>
            </div>
        </div>

        {{-- الإحصائيات المالية الشاملة --}}
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fa fa-chart-line text-primary"></i> {{ __('dashboard.Financial_Summary') }}</h6>
                    <small class="text-muted">{{ __('dashboard.Completed_Orders_Only') }} (status=5)</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#e7f5ff;">
                                <div class="card-body text-center">
                                    <i class="fa fa-money-bill-wave text-info mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Total_Sales') }}</h6>
                                    <h3 class="mb-0 text-info">{{ number_format($financial_summary['total_sales'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#fff4e6;">
                                <div class="card-body text-center">
                                    <i class="fa fa-shield-alt text-warning mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Deposit_Amount') }}</h6>
                                    <h3 class="mb-0 text-warning">{{ number_format($financial_summary['total_deposit_collected'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#f3f0ff;">
                                <div class="card-body text-center">
                                    <i class="fa fa-percentage text-purple mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Commission_Trader') }}</h6>
                                    <h3 class="mb-0 text-purple">{{ number_format($financial_summary['total_commission_trader'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#e6fcf5;">
                                <div class="card-body text-center">
                                    <i class="fa fa-motorcycle text-success mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Commission_Driver') }}</h6>
                                    <h3 class="mb-0 text-success">{{ number_format($financial_summary['total_commission_driver'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#fff0f6;">
                                <div class="card-body text-center">
                                    <i class="fa fa-cut text-danger mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Slaughter_Service_Total') }}</h6>
                                    <h3 class="mb-0 text-danger">{{ number_format($financial_summary['total_slaughter'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card" style="background:#e3fafc;">
                                <div class="card-body text-center">
                                    <i class="fa fa-cash-register text-teal mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Remaining_Amount') }}</h6>
                                    <h3 class="mb-0 text-teal">{{ number_format($financial_summary['total_remaining'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fa fa-check-circle text-secondary mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Completed_Orders') }}</h6>
                                    <h3 class="mb-0">{{ $financial_summary['total_orders_completed'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fa fa-calendar-day text-primary mb-1" style="font-size:20px;"></i>
                                    <h6 class="mb-0 text-muted">{{ __('dashboard.Today_Sales') }}</h6>
                                    <h3 class="mb-0 text-primary">{{ number_format($today_financial['sales'], 2) }} {{ __('dashboard.SAR') }}</h3>
                                    <small class="text-muted">{{ $today_financial['orders_count'] }} {{ __('dashboard.orders') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- إحصائيات طرق الدفع --}}
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fa fa-credit-card text-success"></i> {{ __('dashboard.Payment_Methods_Stats') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $methods = [
                                'cash'   => ['label' => __('dashboard.Cash_Payment'),     'color' => 'success', 'icon' => 'fa-money-bill-wave'],
                                'online' => ['label' => __('dashboard.Online_Payment'), 'color' => 'info',    'icon' => 'fa-credit-card'],
                                'wallet' => ['label' => __('dashboard.Wallet_Payment'),   'color' => 'warning', 'icon' => 'fa-wallet'],
                            ];
                        @endphp
                        @foreach ($methods as $key => $meta)
                            @php $stat = $payment_methods_stats[$key] ?? ['count' => 0, 'total' => 0]; @endphp
                            <div class="col-md-4 mb-2">
                                <div class="card border-{{ $meta['color'] }} border">
                                    <div class="card-body text-center">
                                        <i class="fa {{ $meta['icon'] }} text-{{ $meta['color'] }} mb-1" style="font-size:24px;"></i>
                                        <h6 class="mb-0">{{ $meta['label'] }}</h6>
                                        <h4 class="text-{{ $meta['color'] }}">{{ number_format($stat['total'], 2) }} {{ __('dashboard.SAR') }}</h4>
                                        <small class="text-muted">{{ $stat['count'] }} {{ __('dashboard.orders') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- أحدث الطلبات (مع التاجر) --}}
        <div class="col-xl-6 col-lg-12 mt-3">
            <div class="card mg-b-20">
                <div class="card-header">
                    <h4 class="card-title mg-b-0">{{ __('dashboard.Recent_Orders') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.Order_number') }}</th>
                                    <th>{{ __('dashboard.Client_Name') }}</th>
                                    <th>{{ __('dashboard.Trader_Name') }}</th>
                                    <th>{{ __('dashboard.Total') }}</th>
                                    <th>{{ __('dashboard.Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recently_last_five_order as $order)
                                <tr>
                                    <td><a href="{{ route('orders.show', $order->id) }}">#{{ $order->order_number }}</a></td>
                                    <td>{{ $order->user?->name ?? '—' }}</td>
                                    <td>
                                        @if($order->trader)
                                            <a href="{{ route('traders.show', $order->trader->id) }}" class="text-info">{{ $order->trader->name }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($order->final_price, 2) }}</td>
                                    <td><span class="badge badge-{{ $order->status?->color ?? 'secondary' }}">{{ $order->status?->name }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- أحدث التقييمات (تجار وسائقين) --}}
        <div class="col-xl-6 col-lg-12 mt-3">
            <div class="card mg-b-20">
                <div class="card-header">
                    <h4 class="card-title mg-b-0">{{ __('dashboard.Latest_Reviews') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.Client_Name') }}</th>
                                    <th>{{ __('dashboard.Rating') }}</th>
                                    <th>{{ __('dashboard.Type') }}</th>
                                    <th>{{ __('dashboard.Rated_Entity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recently_all_reviews as $review)
                                <tr>
                                    <td>{{ $review->client?->name ?? '—' }}</td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star {{ $review->rating >= $i ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                    <td>
                                        @if($review instanceof \App\Models\DriverReview)
                                            <span class="badge badge-primary">{{ __('dashboard.Driver') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('dashboard.Trader') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($review instanceof \App\Models\DriverReview)
                                            {{ $review->driver?->name ?? '—' }}
                                        @else
                                            {{ $review->order?->trader?->name ?? '—' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- الأكثر مبيعاً --}}
        <div class="col-xl-6 col-lg-12 mt-3">
            <div class="card mg-b-20">
                <div class="card-header">
                    <h4 class="card-title mg-b-0">{{ __('dashboard.Top_Products') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.Product') }}</th>
                                    <th>{{ __('dashboard.Quantity_Sold') }}</th>
                                    <th>{{ __('dashboard.Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products_most_purchased_ten as $product)
                                <tr>
                                    <td>
                                        @if($product->product)
                                            <img src="{{ $product->product->image_url }}" style="width: 30px; height: 30px; border-radius: 50%;" alt="">
                                            {{ $product->product->name }}
                                        @endif
                                    </td>
                                    <td>{{ $product->total_qty }}</td>
                                    <td>{{ number_format($product->total_revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- المنتجات قارفت على النفاد --}}
        <div class="col-xl-6 col-lg-12 mt-3">
            <div class="card mg-b-20">
                <div class="card-header">
                    <h4 class="card-title mg-b-0">{{ __('dashboard.Expiring_Products') }} ({{ $expired_threshold }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.Product') }}</th>
                                    <th>{{ __('dashboard.Quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expire_products_quantity as $product)
                                <tr>
                                    <td>
                                        <img src="{{ $product->image_url }}" style="width: 30px; height: 30px; border-radius: 50%;" alt="">
                                        {{ $product->name }}
                                    </td>
                                    <td><span class="badge badge-warning">{{ $product->quantity }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endcan
@endsection
