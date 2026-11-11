@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Admin_Wallet'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Admin_Wallet') }}</h4>
        </div>
    </div>
@endsection
@section('content')
    <div class="row mb-3">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center">
            <h3>{{ number_format($summary->total_revenue, 2) }}</h3><small>{{ __('dashboard.Platform_Revenue') }}</small>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center">
            <h3>{{ number_format($summary->held_deposits, 2) }}</h3><small>{{ __('dashboard.Held_Deposits') }}</small>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body text-center">
            <h3>{{ number_format($summary->commission_trader, 2) }}</h3><small>{{ __('dashboard.Trader_Commissions') }}</small>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-info text-white"><div class="card-body text-center">
            <h3>{{ number_format($summary->commission_driver, 2) }}</h3><small>{{ __('dashboard.Driver_Commissions') }}</small>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body text-center">
            <h3>{{ number_format($summary->forfeited, 2) }}</h3><small>{{ __('dashboard.Forfeited_Deposits') }}</small>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Wallet_Transactions') }}</h6></div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <select name="transaction_type" class="form-control">
                        <option value="">{{ __('dashboard.All') }}</option>
                        @foreach ($types as $t)
                            <option value="{{ $t }}" @selected(request('transaction_type') === $t)>{{ __('dashboard.tx_' . $t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
                <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
                <div class="col-md-3"><button class="btn btn-primary btn-block">{{ __('dashboard.Search') }}</button></div>
            </form>

            @if ($transactions->count())
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.Date') }}</th>
                                <th>{{ __('dashboard.Type') }}</th>
                                <th>{{ __('dashboard.User') }}</th>
                                <th>{{ __('dashboard.Order') }}</th>
                                <th>{{ __('dashboard.Amount') }}</th>
                                <th>{{ __('dashboard.Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $t)
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ $t->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ __('dashboard.tx_' . $t->transaction_type) }}</td>
                                    <td>{{ $t->wallet?->user?->name ?? '—' }}</td>
                                    <td>
                                        @if ($t->order)
                                            <a href="{{ route('orders.show', $t->order->id) }}">{{ $t->order->order_number }}</a>
                                        @else — @endif
                                    </td>
                                    <td class="{{ $t->type === 'debit' ? 'text-danger' : 'text-success' }}" dir="ltr">
                                        {{ $t->type === 'debit' ? '-' : '+' }}{{ number_format($t->amount, 2) }}
                                    </td>
                                    <td style="max-width:260px; white-space: normal;">{{ $t->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            @else
                @include('dashboard.includes.alerts.nodata')
            @endif
        </div>
    </div>
@endsection
