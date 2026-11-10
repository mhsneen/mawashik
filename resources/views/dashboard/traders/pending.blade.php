@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Traders_Pending'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Traders_Pending_Approval') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Pending_Approvals') }}</span>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('traders.index') }}" class="btn btn-primary ml-2">
                <i class="fa fa-list"></i> {{ __('dashboard.All_Traders') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($traders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Trader_Name') }}</th>
                                    <th>{{ __('dashboard.Mobile') }}</th>
                                    <th>{{ __('dashboard.Store_Address') }}</th>
                                    <th>{{ __('dashboard.City') }}</th>
                                    <th>{{ __('dashboard.Commercial_Register') }}</th>
                                    <th>{{ __('dashboard.Request_Date') }}</th>
                                    <th>{{ __('dashboard.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($traders as $trader)
                                <tr class="table-warning">
                                    <td>{{ $trader->id }}</td>
                                    <td>{{ $trader->name }}</td>
                                    <td>{{ $trader->mobile }}</td>
                                    <td>{{ $trader->store_address }}</td>
                                    <td>{{ $trader->city?->translate(app()->getLocale())?->name }}</td>
                                    <td>{{ $trader->commercial_register }}</td>
                                    <td>{{ $trader->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('traders.show', $trader->id) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('dashboard.View') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('traders.edit', $trader->id) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('dashboard.Edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('traders.approve', $trader->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm" onclick="return confirm('{{ __('dashboard.Confirm_Approve') }}')">
                                                <i class="fa fa-check"></i> {{ __('dashboard.Approve') }}
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                data-toggle="modal" data-target="#reject-modal-{{ $trader->id }}">
                                            <i class="fa fa-times"></i> {{ __('dashboard.Reject') }}
                                        </button>
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

    {{-- Reject Modal --}}
    @foreach($traders as $trader)
    <div class="modal fade" id="reject-modal-{{ $trader->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('traders.reject', $trader->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.Reject_Trader') }}: {{ $trader->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>{{ __('dashboard.Rejection_Reason') }}</label>
                        <textarea name="note" class="form-control" required rows="3" placeholder="{{ __('dashboard.Rejection_Reason_Placeholder') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.Reject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
