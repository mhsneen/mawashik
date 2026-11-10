@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Deposits_Settings'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Deposits_Settings') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('deposits.report') }}" class="btn btn-info ml-2">
                <i class="fa fa-chart-line"></i> {{ __('dashboard.Report') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Deposit_Settings') }}</h6></div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    <form action="{{ route('deposits.updateSettings') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('dashboard.Deposit_Percentage') }} %</label>
                            <input type="number" name="deposit_percentage" step="0.01" min="0" max="100" class="form-control"
                                   value="{{ $settings['deposit_percentage']?->plain_value ?? 15 }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.Min_Deposit_Amount') }}</label>
                                    <input type="number" name="deposit_min_amount" step="0.01" min="0" class="form-control"
                                           value="{{ $settings['deposit_min_amount']?->plain_value ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.Max_Deposit_Amount') }}</label>
                                    <input type="number" name="deposit_max_amount" step="0.01" min="0" class="form-control"
                                           value="{{ $settings['deposit_max_amount']?->plain_value ?? 0 }}">
                                    <small class="text-muted">0 = لا حد أقصى</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('dashboard.Deposit_Refund_Before') }}</label>
                            <select name="deposit_refund_before" class="form-control">
                                <option value="anytime" @if(($settings['deposit_refund_before']?->plain_value ?? 'preparing')==='anytime') selected @endif>
                                    {{ __('dashboard.Refund_Anytime') }}</option>
                                <option value="preparing" @if(($settings['deposit_refund_before']?->plain_value ?? 'preparing')==='preparing') selected @endif>
                                    {{ __('dashboard.Refund_Before_Preparing') }}</option>
                                <option value="never" @if(($settings['deposit_refund_before']?->plain_value ?? 'preparing')==='never') selected @endif>
                                    {{ __('dashboard.Refund_Never') }}</option>
                            </select>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="deposit_active" class="form-check-input" id="depActive" {{ $settings['deposit_active']?->plain_value=='1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="depActive">{{ __('dashboard.Deposit_Active') }}</label>
                        </div>
                        <button class="btn btn-primary btn-block mt-2">{{ __('dashboard.Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
