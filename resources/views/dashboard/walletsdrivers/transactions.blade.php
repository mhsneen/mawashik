@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Wallet_Transactions') }} - {{ $wallet->user->name ?? '' }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
    <style>
        .type-badge { padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 12px; color: #fff; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 11px; color: #fff; }
        .owner-card { border-radius: 16px; background: #fff; color: #212529; border: 1px solid #e9ecef; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
        .stat-mini { border-radius: 12px; padding: 14px 16px; background: #f8f9fb; text-align: center; }
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
            <div class="pr-1 mb-3 mb-xl-0 ml-3 mr-3">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#ModalSettlement">
                    <i class="fa fa-exchange-alt mr-1"></i> {{ __('dashboard.Manual_Settlement') }}
                </button>
            </div>
            <a href="{{ route('walletsdrivers') }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
        </div>
    </div>
@endsection
@section('content')

    {{-- مودال التسوية اليدوية --}}
    <div class="modal fade" id="ModalSettlement" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('walletsdrivers.settle', $wallet->id) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('dashboard.Manual_Settlement') }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">{{ __('dashboard.Settlement_Direction') }} <span class="tx-danger">*</span></label>
                            <select class="form-control" name="direction" required>
                                <option value="pay_driver">{{ __('dashboard.Pay_Driver') }}</option>
                                <option value="collect_cash">{{ __('dashboard.Collect_Cash') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('dashboard.Amount') }} <span class="tx-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('dashboard.Settlement_Note') }}</label>
                            <textarea class="form-control" name="note" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">{{ __('dashboard.Save') }}</button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">{{ __('dashboard.Close') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card owner-card mb-3">
                <div class="card-body text-center py-4">
                    <img alt=""
                        src="@if (empty($wallet->user->image)) {{ URL::asset('assets/dashboard/img/faces/person-icon.png') }}@else{{ URL::asset('uploads/users/thumbnail/' . $wallet->user->image . '') }}@endif"
                        style="width:80px;height:80px;border-radius:50%;border:3px solid #0d6efd;object-fit:cover;">
                    <h5 class="mt-3 mb-0" style="color:#0d6efd;font-weight:700;">{{ $wallet->user->name ?? '-' }}</h5>
                    <p class="mb-3" style="color:#6c757d;">{{ $wallet->user->mobile ?? '-' }}</p>
                    <h6 class="mb-1" style="color:#495057;font-size:14px;">{{ __('dashboard.Current_Balance') }}</h6>
                    <h2 class="font-weight-bold mb-0" style="color: {{ $wallet->balance < 0 ? '#dc3545' : '#198754' }};">
                        {{ number_format($wallet->balance, 2) }} {{ __('dashboard.SAR') }}
                    </h2>
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

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">{{ __('dashboard.Withdrawal_Requests') }}</h5></div>
                @if (count($withdrawalRequests) > 0)
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.Amount') }}</th>
                                        <th>{{ __('dashboard.Status') }}</th>
                                        <th>{{ __('dashboard.Driver_Note') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($withdrawalRequests as $req)
                                        <tr>
                                            <td class="font-weight-bold">{{ number_format($req->amount, 2) }}</td>
                                            <td><span class="status-badge bg-{{ $req->status_color }}">{{ $req->status_label }}</span></td>
                                            <td>{{ $req->driver_note ?? '—' }}</td>
                                            <td>{{ $req->created_at->diffForHumans() }}</td>
                                            <td>
                                                @if ($req->status === 'pending')
                                                    <form method="POST" action="{{ route('walletsdrivers.approveWithdrawal', $req->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success" type="submit">{{ __('dashboard.Approve') }}</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('walletsdrivers.rejectWithdrawal', $req->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger" type="submit">{{ __('dashboard.Reject') }}</button>
                                                    </form>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $withdrawalRequests->links() }}</div>
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('dashboard.Wallet_Transactions') }}</h5></div>
                @if (count($transactions) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.Type_of_movement') }}</th>
                                        <th>{{ __('dashboard.Amount') }}</th>
                                        <th>{{ __('dashboard.Description') }}</th>
                                        <th>{{ __('dashboard.Order_Number') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $tx)
                                        <tr>
                                            <td><span class="type-badge" style="background-color: {{ $tx->type_color }};">{{ $tx->type_translated }}</span></td>
                                            <td class="font-weight-bold {{ $tx->type === 'debit' ? 'text-danger' : 'text-success' }}">{{ $tx->amount_signed }}</td>
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
                        <div class="mt-3">{{ $transactions->links() }}</div>
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>
        </div>
    </div>
@endsection
