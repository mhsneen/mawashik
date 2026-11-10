@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Commissions_Report'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Commissions_Report') }}</h4>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals->orders_count, 0) }}</h3>
                            <small>{{ __('dashboard.Delivered_Orders') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals->commission_trader_total, 2) }}</h3>
                            <small>{{ __('dashboard.Trader_Commission_Total') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals->commission_driver_total, 2) }}</h3>
                            <small>{{ __('dashboard.Driver_Commission_Total') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals->final_price_total, 2) }}</h3>
                            <small>{{ __('dashboard.Revenue_Total') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Order_Number') }}</th>
                                    <th>{{ __('dashboard.Trader') }}</th>
                                    <th>{{ __('dashboard.Driver') }}</th>
                                    <th>{{ __('dashboard.Final_Price') }}</th>
                                    <th>{{ __('dashboard.Trader_Commission') }}</th>
                                    <th>{{ __('dashboard.Driver_Commission') }}</th>
                                    <th>{{ __('dashboard.Delivered_At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $o)
                                <tr>
                                    <td>{{ $o->id }}</td>
                                    <td>{{ $o->order_number }}</td>
                                    <td>{{ $o->trader?->name }}</td>
                                    <td>{{ $o->driver?->name }}</td>
                                    <td>{{ number_format($o->final_price, 2) }}</td>
                                    <td class="text-success">{{ number_format($o->commission_trader, 2) }}</td>
                                    <td class="text-info">{{ number_format($o->commission_driver, 2) }}</td>
                                    <td>{{ $o->delivered_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $orders->links() }}
                    @else
                        @include('dashboard.includes.alerts.nodata')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
