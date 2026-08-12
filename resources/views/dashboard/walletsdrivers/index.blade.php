@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Driver_Wallets') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .wallet-card,
        .wallet-card-header,
        .wallet-balance-box,
        .wallet-stats,
        .wallet-card-footer {
            color: #343a40 !important;
        }
        .wallet-card {
            border-radius: 16px;
            border: 1px solid #eef0f4;
            transition: all .25s ease;
            overflow: hidden;
            height: 100%;
            background: #fff !important;
        }
        .wallet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(13, 110, 253, .12);
            border-color: #cfe0ff;
        }
        .wallet-card-header {
            background: linear-gradient(135deg, #f4f7ff, #eef2ff) !important;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .wallet-card-header h6 { color: #1c273c !important; }
        .wallet-card-header small { color: #6c757d !important; }
        .wallet-avatar {
            width: 54px; height: 54px; border-radius: 50%;
            object-fit: cover; border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dbe4ff;
        }
        .wallet-balance-box {
            background: #fff !important;
            border-radius: 12px;
            padding: 14px 16px;
            margin: 16px 18px 0;
            text-align: center;
            border: 1px dashed #d9e2ff;
        }
        .wallet-balance-box .text-muted { color: #6c757d !important; }
        .wallet-balance-value { font-size: 24px; font-weight: 800; color: #198754 !important; }
        .wallet-balance-value.negative { color: #dc3545 !important; }
        .wallet-balance-value.zero { color: #6c757d !important; }
        .wallet-stats {
            display: flex; justify-content: space-between;
            padding: 14px 20px 6px; font-size: 12.5px; color: #6c757d !important;
        }
        .wallet-card-footer { padding: 14px 20px 20px; }
        .summary-card { border-radius: 16px; border: none; overflow: hidden; color: #fff !important; }
        .summary-card.bg-blue-gradient { background: linear-gradient(135deg, #0d6efd, #00b4d8) !important; }
        .summary-card.bg-green-gradient { background: linear-gradient(135deg, #198754, #20c997) !important; }
        .summary-card.bg-warning-gradient { background: linear-gradient(135deg, #f5b700, #ff8c00) !important; }
        .summary-card .card-body, .summary-card h2, .summary-card h6 { color: #fff !important; }
        .summary-card h6 { opacity: 0.85; }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Driver_Wallets') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('walletsdrivers.withdrawalRequests') }}" class="btn btn-warning">
                    <i class="fa fa-hand-holding-usd mr-1"></i>
                    {{ __('dashboard.Withdrawal_Requests') }}
                    @if ($pendingRequestsCount > 0)
                        <span class="badge badge-light ml-1">{{ $pendingRequestsCount }}</span>
                    @endif
                </a>
            </div>
            <div class="pr-1 mb-3 mb-xl-0">
                <a aria-controls="collapseExample" aria-expanded="false" data-toggle="collapse" href="#collapseExample"
                    type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-magnify"></i></a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card summary-card bg-blue-gradient">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">{{ __('dashboard.Total_Wallets_Balance') }}</h6>
                                <h2 class="mb-0 font-weight-bold">{{ number_format($totalBalance, 2) }} {{ __('dashboard.SAR') }}</h2>
                            </div>
                            <i class="fa fa-wallet" style="font-size: 34px; opacity: .7;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card summary-card bg-green-gradient">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">{{ __('dashboard.Total_Active_Wallets') }}</h6>
                                <h2 class="mb-0 font-weight-bold">{{ $totalWalletsCount }}</h2>
                            </div>
                            <i class="fa fa-users" style="font-size: 34px; opacity: .7;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card collapse" id="collapseExample">
                <div class="card-body">
                    <form class="form-horizontal" method="GET" action="{{ route('walletsdrivers') }}">
                        <div class="row row-sm mg-b-20">
                            <div class="col-lg-6">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Name') }}</p>
                                <input class="form-control" name="search_name" type="text" value="{{ request('search_name') }}">
                            </div>
                            <div class="col-lg-6">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Mobile') }}</p>
                                <input class="form-control" name="search_mobile" type="text" value="{{ request('search_mobile') }}">
                            </div>
                        </div>
                        <div class="card-footer text-left">
                            <button class="btn btn-dark" type="submit">{{ __('dashboard.Search') }} <i class="mdi mdi-magnify"></i></button>
                            <a href="{{ route('walletsdrivers') }}" class="btn btn-danger">{{ __('dashboard.Clear_Search') }} <i class="mdi mdi-cancel"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            @if (count($wallets) > 0)
                <div class="row">
                    @foreach ($wallets as $wallet)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card wallet-card">
                                <div class="wallet-card-header">
                                    <img class="wallet-avatar"
                                        src="@if (empty($wallet->user->image)) {{ URL::asset('assets/dashboard/img/faces/person-icon.png') }}@else{{ URL::asset('uploads/users/thumbnail/' . $wallet->user->image . '') }}@endif"
                                        alt="">
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">{{ $wallet->user->name ?? '-' }}</h6>
                                        <small class="text-muted">{{ $wallet->user->mobile ?? '-' }}</small>
                                    </div>
                                </div>

                                <div class="wallet-balance-box">
                                    <div class="text-muted tx-12 mb-1">
                                        {{ $wallet->balance > 0 ? __('dashboard.Store_Owes_Driver') : ($wallet->balance < 0 ? __('dashboard.Driver_Owes_Store') : __('dashboard.Balanced')) }}
                                    </div>
                                    <div class="wallet-balance-value {{ $wallet->balance < 0 ? 'negative' : ($wallet->balance == 0 ? 'zero' : '') }}">
                                        {{ number_format($wallet->balance, 2) }} {{ __('dashboard.SAR') }}
                                    </div>
                                </div>

                                <div class="wallet-stats">
                                    <span><i class="fa fa-exchange-alt mr-1"></i> {{ __('dashboard.Movements_Count') }}: <strong>{{ $wallet->transactions_count }}</strong></span>
                                    <span>
                                        <i class="fa fa-clock mr-1"></i>
                                        @if ($wallet->transactions_max_created_at)
                                            {{ \Carbon\Carbon::parse($wallet->transactions_max_created_at)->diffForHumans() }}
                                        @else
                                            {{ __('dashboard.No_Transaction_Yet') }}
                                        @endif
                                    </span>
                                </div>

                                <div class="wallet-card-footer d-flex" style="gap:8px;">
                                    <a href="{{ route('walletsdrivers.transactions', $wallet->id) }}"
                                        class="btn btn-primary flex-fill">
                                        <i class="fa fa-list mr-1"></i> {{ __('dashboard.View_Transactions') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    {{ $wallets->withQueryString()->links() }}
                </div>
            @else
                @include('dashboard.includes.alerts.nodata')
            @endif

        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
@endsection
