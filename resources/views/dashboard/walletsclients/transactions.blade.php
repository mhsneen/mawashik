@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Wallet_Transactions') ?? __('dashboard.Wallet Transactions') }} - {{ $wallet->user->name ?? '' }}
@endsection
@section('css')
    <style>
        .type-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            color: #fff;
        }
        .owner-card {
            border-radius: 16px;
            background: #ffffff;
            color: #212529;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        .stat-mini {
            border-radius: 12px;
            padding: 14px 16px;
            background: #f8f9fb;
            text-align: center;
        }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.View_Transactions') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $wallet->user->name ?? '' }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card owner-card mb-3">
                <div class="card-body text-center py-4">
                    <img alt=""
                        src="@if (empty($wallet->user->image)) {{ URL::asset('assets/dashboard/img/faces/person-icon.png') }}@else{{ URL::asset('uploads/users/thumbnail/' . $wallet->user->image . '') }}@endif"
                        style="width:80px;height:80px;border-radius:50%;border:3px solid #0d6efd;object-fit:cover;">
                    <h5 class="mt-3 mb-0" style="color: #0d6efd; font-weight: 700;">{{ $wallet->user->name ?? '-' }}</h5>
                    <p class="mb-3" style="color: #6c757d;">{{ $wallet->user->mobile ?? '-' }}</p>
                    <h6 class="mb-1" style="color: #495057; font-size: 14px;">{{ __('dashboard.Current_Balance') }}</h6>
                    <h2 class="font-weight-bold mb-0" style="color: #198754;">{{ number_format($wallet->balance, 2) }} {{ __('dashboard.SAR') }}</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="stat-mini mb-3">
                        <small class="text-muted d-block mb-1">{{ __('dashboard.Total_Credit') }}</small>
                        <h5 class="text-success mb-0">+ {{ number_format($totalCredit, 2) }}</h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-mini mb-3">
                        <small class="text-muted d-block mb-1">{{ __('dashboard.Total_Debit') }}</small>
                        <h5 class="text-danger mb-0">- {{ number_format($totalDebit, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card">
                @if (count($transactions) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.Type_of_movement') ?? __('dashboard.Type of movement') }}</th>
                                        <th>{{ __('dashboard.Amount') }}</th>
                                        <th>{{ __('dashboard.Description') }}</th>
                                        <th>{{ __('dashboard.Order_Number') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $tx)
                                        <tr>
                                            <td>
                                                <span class="type-badge" style="background-color: {{ $tx->type_color }};">
                                                    {{ $tx->type_translated }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold {{ $tx->type === 'debit' ? 'text-danger' : 'text-success' }}">
                                                {{ $tx->amount_signed }}
                                            </td>
                                            <td>{{ $tx->description }}</td>
                                            <td>
                                                @if ($tx->order)
                                                    <a href="{{ route('orders.show', $tx->order_id) }}">#{{ $tx->order->order_number }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $tx->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>
        </div>
    </div>
@endsection
