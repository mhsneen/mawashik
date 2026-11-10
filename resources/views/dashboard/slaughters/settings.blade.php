@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Slaughter_Service'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Slaughter_Service') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('slaughters.report') }}" class="btn btn-info ml-2">
                <i class="fa fa-chart-bar"></i> {{ __('dashboard.Report') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fa fa-cut text-danger"></i> {{ __('dashboard.Slaughter_Service_Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('slaughters.settings.update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="ckbox">
                                <input type="checkbox" name="slaughter_active" value="1" {{ $slaughter_active ? 'checked' : '' }}>
                                <span style="font-size: 16px; font-weight: bold;">{{ __('dashboard.Activate_Slaughter_Service') }}</span>
                            </label>
                            <small class="d-block text-muted mt-1">{{ __('dashboard.Activate_Slaughter_Service_Hint') }}</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>{{ __('dashboard.Slaughter_Default_Price') }} ({{ __('dashboard.SAR') }})</label>
                            <div class="input-group">
                                <input type="number" name="slaughter_default_price" class="form-control" step="0.01" min="0" value="{{ $slaughter_default_price ?? 0 }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ __('dashboard.SAR') }}</span>
                                </div>
                            </div>
                            <small class="d-block text-muted mt-1">{{ __('dashboard.Slaughter_Default_Price_Hint') }}</small>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-save"></i> {{ __('dashboard.Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
