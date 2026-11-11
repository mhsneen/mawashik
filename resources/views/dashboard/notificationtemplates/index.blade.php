@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Notification_Templates'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Notification_Templates') }}</h4>
        </div>
    </div>
@endsection
@section('content')
    @include('dashboard.includes.alerts.success')
    <div class="alert alert-info">{{ __('dashboard.Notification_Templates_Hint') }}</div>

    @foreach ($templates as $t)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <code>{{ $t->key }}</code>
                    @if ($t->customized)<span class="badge badge-warning">{{ __('dashboard.Customized') }}</span>@endif
                </div>
                <div dir="ltr">
                    @foreach ($t->placeholders as $p)
                        <span class="badge badge-secondary">:{{ $p }}</span>
                    @endforeach
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('notificationtemplates.update', $t->key) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>{{ __('dashboard.Arabic') }}</label>
                            <textarea name="text_ar" rows="2" class="form-control" placeholder="{{ $t->default_ar }}">{{ $t->text_ar }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('dashboard.English') }}</label>
                            <textarea name="text_en" rows="2" class="form-control" dir="ltr" placeholder="{{ $t->default_en }}">{{ $t->text_en }}</textarea>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm">{{ __('dashboard.Save') }}</button>
                        @if ($t->customized)
                            <button type="submit" formaction="{{ route('notificationtemplates.reset', $t->key) }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('dashboard.Reset_To_Default') }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
