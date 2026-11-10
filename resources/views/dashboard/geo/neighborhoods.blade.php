@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Neighborhoods'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Neighborhoods_Management') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('geo.countries.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Countries') }}</a>
            <a href="{{ route('geo.cities.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Cities') }}</a>
            <a href="{{ route('geo.areas.index') }}" class="btn btn-info ml-2">{{ __('dashboard.Areas') }}</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Add_Neighborhood') }}</h6></div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    <form action="{{ route('geo.neighborhoods.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('dashboard.Area') }}</label>
                            <select name="area_id" class="form-control" required>
                                @foreach($areas as $a)
                                <option value="{{ $a->id }}">{{ $a->translate('ar')?->name }} — {{ $a->city?->translate('ar')?->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    @if($neighborhoods->count() > 0)
                    <table class="table table-bordered">
                        <thead><tr><th>#</th><th>{{ __('dashboard.Name') }}</th><th>{{ __('dashboard.Area') }}</th><th>{{ __('dashboard.City') }}</th><th>{{ __('dashboard.Action') }}</th></tr></thead>
                        <tbody>
                            @foreach($neighborhoods as $index => $n )
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $n->translate(app()->getLocale())?->name }}</td>
                                <td>{{ $n->area?->translate('ar')?->name }}</td>
                                <td>{{ $n->area?->city?->translate('ar')?->name }}</td>
                                <td>
                                    <form action="{{ route('geo.neighborhoods.destroy', $n->id) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $neighborhoods->links() }}
                    @else @include('dashboard.includes.alerts.nodata') @endif
                </div>
            </div>
        </div>
    </div>
@endsection
