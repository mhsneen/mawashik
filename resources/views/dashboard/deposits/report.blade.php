@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Deposits_Report'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Deposits_Report') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('deposits.settings') }}" class="btn btn-warning ml-2">
                <i class="fa fa-cog"></i> {{ __('dashboard.Settings') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->total_deposit, 2) }}</h3>
                    <small>{{ __('dashboard.Total_Deposits') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->pending, 2) }}</h3>
                    <small>{{ __('dashboard.Pending_Deposits') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->refunded, 2) }}</h3>
                    <small>{{ __('dashboard.Refunded_Deposits') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($totals->released, 2) }}</h3>
                    <small>{{ __('dashboard.Released_Deposits') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
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
                                    <th>{{ __('dashboard.Client') }}</th>
                                    <th>{{ __('dashboard.Trader') }}</th>
                                    <th>{{ __('dashboard.Final_Price') }}</th>
                                    <th>{{ __('dashboard.Deposit_Amount') }}</th>
                                    <th>{{ __('dashboard.Deposit_Status') }}</th>
                                    <th>{{ __('dashboard.Paid_At') }}</th>
                                    <th>{{ __('dashboard.Refunded_At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $o)
                                <tr>
                                    <td>{{ $o->id }}</td>
                                    <td>{{ $o->order_number }}</td>
                                    <td>{{ $o->user?->name }}</td>
                                    <td>{{ $o->trader?->name }}</td>
                                    <td>{{ number_format($o->final_price, 2) }}</td>
                                    <td><strong>{{ number_format($o->deposit_amount, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $labels = [
                                                'none' => ['secondary', 'dashboard.Deposit_None'],
                                                'paid' => ['warning', 'dashboard.Deposit_Paid'],
                                                'refunded' => ['info', 'dashboard.Deposit_Refunded'],
                                                'partial_refund' => ['danger', 'dashboard.Deposit_Partial_Refund'],
                                                'released' => ['success', 'dashboard.Deposit_Released'],
                                            ];
                                            $status = $labels[$o->deposit_status] ?? ['secondary', 'dashboard.Deposit_None'];
                                        @endphp
                                        <span class="badge badge-{{ $status[0] }}">{{ __($status[1]) }}</span>
                                    </td>
                                    <td>{{ $o->deposit_paid_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $o->deposit_refunded_at?->format('Y-m-d H:i') }}</td>
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
