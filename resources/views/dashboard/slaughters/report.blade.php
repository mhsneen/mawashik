@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Slaughter_Report'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Slaughter_Service_Report') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('slaughters.settings') }}" class="btn btn-warning ml-2">
                <i class="fa fa-cog"></i> {{ __('dashboard.Settings') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->products_count, 0) }}</h3>
                    <small>{{ __('dashboard.Slaughter_Products') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->orders_count, 0) }}</h3>
                    <small>{{ __('dashboard.Slaughter_Orders') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->slaughter_total_sum, 2) }}</h3>
                    <small>{{ __('dashboard.Slaughter_Revenue_Total') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Orders_With_Slaughter') }}</h6></div>
                <div class="card-body">
                    @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Order_Number') }}</th>
                                    <th>{{ __('dashboard.Client') }}</th>
                                    <th>{{ __('dashboard.Trader') }}</th>
                                    <th>{{ __('dashboard.Slaughter_Total') }}</th>
                                    <th>{{ __('dashboard.Order_Status') }}</th>
                                    <th>{{ __('dashboard.Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $o)
                                <tr>
                                    <td>{{ $o->id }}</td>
                                    <td>{{ $o->order_number }}</td>
                                    <td>{{ $o->user?->name }}</td>
                                    <td>{{ $o->trader?->name }}</td>
                                    <td>{{ number_format($o->slaughter_total, 2) }}</td>
                                    <td>{{ $o->status?->translate(app()->getLocale())?->name }}</td>
                                    <td>{{ $o->created_at?->format('Y-m-d H:i') }}</td>
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
