@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Withdrawal_Requests') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .status-badge { padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 11px; color: #fff; }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Withdrawal_Requests') }}</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('walletsdrivers') }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('walletsdrivers.withdrawalRequests') }}">
                        <div class="row">
                            <div class="col-lg-4">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Status') }}</p>
                                <select class="form-control select2" name="search_status">
                                    <option value="">{{ __('dashboard.Choose') }}</option>
                                    <option value="pending" {{ request('search_status') == 'pending' ? 'selected' : '' }}>{{ __('dashboard.Withdrawal_Status_Pending') }}</option>
                                    <option value="approved" {{ request('search_status') == 'approved' ? 'selected' : '' }}>{{ __('dashboard.Withdrawal_Status_Approved') }}</option>
                                    <option value="rejected" {{ request('search_status') == 'rejected' ? 'selected' : '' }}>{{ __('dashboard.Withdrawal_Status_Rejected') }}</option>
                                    <option value="cancelled" {{ request('search_status') == 'cancelled' ? 'selected' : '' }}>{{ __('dashboard.Withdrawal_Status_Cancelled') }}</option>
                                </select>
                                 <br/> <br/>
                            </div>
                        </div>
                        <div class="card-footer text-left">
                            <button class="btn btn-dark" type="submit">{{ __('dashboard.Search') }}</button>
                            <a href="{{ route('walletsdrivers.withdrawalRequests') }}" class="btn btn-danger">{{ __('dashboard.Clear_Search') }}</a>
                        </div>
                    </form>

                </div>
            </div>

            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card">
                @if (count($requests) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter mb-0">
                                <thead>
                                    <tr>
                                     {{-- ✅ تم تعديل العمود ليعرض السائق أو التاجر --}}
<th>{{ __('dashboard.User') }}</th>
<th>{{ __('dashboard.User_Type') }}</th>
                                        <th>{{ __('dashboard.Amount') }}</th>
                                        <th>{{ __('dashboard.Status') }}</th>
                                        <th>{{ __('dashboard.Driver_Note') }}</th>
                                        <th>{{ __('dashboard.Admin_Note') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $req)
                                        <tr>



                                            <td>
    {{ $req->user?->name ?? '—' }}
</td>
<td>
    @if($req->user && $req->user->hasRole('trader'))
        <span class="badge badge-info">تاجر</span>
    @elseif($req->user && $req->user->hasRole('driver'))
        <span class="badge badge-primary">سائق</span>
    @else
        <span class="badge badge-secondary">—</span>
    @endif
</td>


                                            <td class="font-weight-bold">{{ number_format($req->amount, 2) }}</td>
                                            <td><span class="status-badge bg-{{ $req->status_color }}">{{ $req->status_label }}</span></td>
                                            <td>{{ $req->driver_note ?? '—' }}</td>
                                            <td>{{ $req->admin_note ?? '—' }}</td>
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
                                                    <a href="{{ route('walletsdrivers.transactions', $req->wallet_id) }}" class="btn btn-sm btn-info">{{ __('dashboard.View') }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $requests->withQueryString()->links() }}</div>
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $('.select2').select2({ placeholder: '{{ __('dashboard.Choose') }}', width: '100%' });
    </script>
@endsection
