@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Traders'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Traders') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('traders.create') }}" class="btn btn-primary ml-2">
                <i class="fa fa-plus"></i> {{ __('dashboard.Add_New') }}
            </a>
            <a href="{{ route('traders.pending') }}" class="btn btn-warning ml-2">
                <i class="fa fa-hourglass-half"></i> {{ __('dashboard.Pending_Approvals') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="card-title mb-0">{{ __('dashboard.Traders_List') }}</h6>
                </div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.errors')
                    @include('dashboard.includes.alerts.success')
                    <form method="GET" action="{{ route('traders.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search_name" class="form-control" placeholder="{{ __('dashboard.Name') }}" value="{{ request('search_name') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search_mobile" class="form-control" placeholder="{{ __('dashboard.Mobile') }}" value="{{ request('search_mobile') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="trader_status" class="form-control">
                                    <option value="">{{ __('dashboard.All_Statuses') }}</option>
                                    <option value="pending" @if(request('trader_status')==='pending') selected @endif>{{ __('dashboard.Pending') }}</option>
                                    <option value="approved" @if(request('trader_status')==='approved') selected @endif>{{ __('dashboard.Approved') }}</option>
                                    <option value="suspended" @if(request('trader_status')==='suspended') selected @endif>{{ __('dashboard.Suspended') }}</option>
                                    <option value="rejected" @if(request('trader_status')==='rejected') selected @endif>{{ __('dashboard.Rejected') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> {{ __('dashboard.Search') }}</button>
                            </div>
                        </div>
                    </form>

                    @if($traders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Trader_Name') }}</th>
                                    <th>{{ __('dashboard.Mobile') }}</th>
                                    <th>{{ __('dashboard.City') }}</th>
                                    <th>{{ __('dashboard.Commercial_Register') }}</th>
                                    <th>{{ __('dashboard.Commission_Rate') }}</th>
                                    <th>{{ __('dashboard.Status') }}</th>
                                    <th>{{ __('dashboard.Created_at') }}</th>
                                    <th>{{ __('dashboard.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>


                                    @foreach($traders as $index => $trader)
    <tr>
        <td>{{ $index + 1 }}</td>


                                    <td>{{ $trader->name }}</td>
                                    <td style="direction: ltr">{{ $trader->mobile }}</td>
                                    <td>{{ $trader->city?->translate(app()->getLocale())?->name }}</td>
                                    <td>{{ $trader->commercial_register }}</td>
                                    <td>{{ $trader->commission_rate ?? __('dashboard.Default') }}</td>
                                    <td>
                                        @php
                                            $statusColors = ['pending' => 'warning', 'approved' => 'success', 'suspended' => 'danger', 'rejected' => 'dark'];
                                            $statusLabel = 'dashboard.'.ucfirst($trader->trader_status ?? 'pending');
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$trader->trader_status] ?? 'secondary' }}">
                                            {{ __($statusLabel) }}
                                        </span>
                                    </td>
                                    <td>{{ $trader->created_at?->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('traders.show', $trader->id) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('dashboard.View') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('traders.edit', $trader->id) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('dashboard.Edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($trader->trader_status === 'pending' || $trader->trader_status === 'rejected')
                                            <form action="{{ route('traders.approve', $trader->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __('dashboard.Confirm_Approve') }}')" data-toggle="tooltip" title="{{ __('dashboard.Approve_Trader') }}">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($trader->trader_status === 'approved')
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#suspend-modal-{{ $trader->id }}" data-toggle="tooltip" title="{{ __('dashboard.Suspend_Trader') }}">
                                                <i class="fa fa-ban"></i>
                                            </button>
                                        @endif
                                        @if($trader->trader_status === 'suspended')
                                            <form action="{{ route('traders.reactivate', $trader->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" data-toggle="tooltip" title="{{ __('dashboard.Reactivate_Trader') }}">
                                                    <i class="fa fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $traders->links() }}
                    @else
                        @include('dashboard.includes.alerts.nodata')
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Suspend Modals --}}
    @foreach($traders as $t)
        @if($t->trader_status === 'approved')
        <div class="modal fade" id="suspend-modal-{{ $t->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('traders.suspend', $t->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.Suspend_Trader') }}: {{ $t->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <label>{{ __('dashboard.Rejection_Reason') }}</label>
                            <textarea name="note" class="form-control" required rows="3" placeholder="{{ __('dashboard.Rejection_Reason_Placeholder') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                            <button type="submit" class="btn btn-warning">{{ __('dashboard.Suspend_Trader') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @endforeach
@endsection
