@extends('layouts.admin_dashboard')
@section('title')
    {{ app()->getLocale() == 'ar' ? ($catalog['name_ar'] ?? $item->code) : ($catalog['name_en'] ?? $item->code) }} - {{ __('dashboard.Edit') }}
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">
                    {{ app()->getLocale() == 'ar' ? ($catalog['name_ar'] ?? $item->code) : ($catalog['name_en'] ?? $item->code) }}
                </h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('paymentmethods') }}" class="btn btn-info btn-icon"><i
                    class="typcn typcn-arrow-back-outline"></i></a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-lg-8 mx-auto">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card">
                <div class="card-body">

                    @if (!empty($catalog['docs_url']))
                        <p class="tx-13 text-muted">
                            {{ __('dashboard.Payment_Docs_Link') }}:
                            <a href="{{ $catalog['docs_url'] }}" target="_blank">{{ $catalog['docs_url'] }}</a>
                        </p>
                    @endif

                    <form method="POST" action="{{ route('paymentmethods.update', $item->code) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label class="col-md-3 form-label">{{ __('dashboard.Status') }}</label>
                            <div class="col-md-9">
                                <label class="custom-switch">
                                    <input type="checkbox" name="is_enabled" class="custom-switch-input" value="1"
                                        {{ $item->is_enabled ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">{{ __('dashboard.Enable_Payment_Method') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 form-label">{{ __('dashboard.Payment_Mode') }}</label>
                            <div class="col-md-9">
                                <select name="mode" class="form-control select2">
                                    <option value="test" {{ $item->mode === 'test' ? 'selected' : '' }}>
                                        {{ __('dashboard.Test_Mode') }}</option>
                                    <option value="live" {{ $item->mode === 'live' ? 'selected' : '' }}>
                                        {{ __('dashboard.Live_Mode') }}</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        @foreach ($catalog['fields'] ?? [] as $key => $field)
                            <div class="form-group row">
                                <label class="col-md-3 form-label">
                                    {{ app()->getLocale() == 'ar' ? $field['label_ar'] : $field['label_en'] }}
                                    @if (!empty($field['required']))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-md-9">
                                    <input type="{{ $field['type'] == 'password' ? 'password' : 'text' }}"
                                        name="{{ $key }}" class="form-control"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        value="{{ $field['type'] == 'password' ? '' : old($key, $item->getConfigValue($key)) }}"
                                        autocomplete="new-password">
                                    @if ($field['type'] == 'password' && $item->getConfigValue($key))
                                        <small class="text-muted">{{ __('dashboard.Leave_Empty_Keep_Current') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group row mg-t-20">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
