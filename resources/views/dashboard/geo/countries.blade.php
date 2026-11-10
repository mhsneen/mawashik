@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Countries'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Countries_Management') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('geo.cities.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Cities') }}</a>
            <a href="{{ route('geo.areas.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Areas') }}</a>
            <a href="{{ route('geo.neighborhoods.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Neighborhoods') }}</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Add_Country') }}</h6></div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    <form action="{{ route('geo.countries.store') }}" method="POST">
                        @csrf
                        <div class="form-group"><label>{{ __('dashboard.Name_Arabic') }}</label><input name="name_ar" class="form-control" required></div>
                        <div class="form-group"><label>{{ __('dashboard.Name_English') }}</label><input name="name_en" class="form-control" required></div>
                        <div class="form-check"><input type="hidden" name="is_active" value="0">

<input
    type="checkbox"
    name="is_active"
    class="form-check-input"
    id="cAct"
    value="1"
    checked
>  <label class="form-check-label" for="cAct" style="margin-left: 20px; margin-right:20px">{{ __('dashboard.Active') }}</label>


</div>


<button class="btn btn-primary btn-block mt-2">{{ __('dashboard.Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if($countries->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th><th>{{ __('dashboard.Name_Arabic') }}</th><th>{{ __('dashboard.Name_English') }}</th>
                                <th>{{ __('dashboard.Active') }}</th><th>{{ __('dashboard.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($countries as $index => $c)
    <tr>
        <td>{{ $index + 1 }}</td>
                                <td>{{ $c->translate('ar')?->name }}</td>
                                <td>{{ $c->translate('en')?->name }}</td>
                                <td>{{ $c->is_active ? '✓' : '✗' }}</td>
                                <td>
                                    <form action="{{ route('geo.countries.destroy', $c->id) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('dashboard.Confirm_Delete') }}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $countries->links() }}
                    @else @include('dashboard.includes.alerts.nodata') @endif
                </div>
            </div>
        </div>
    </div>
@endsection
