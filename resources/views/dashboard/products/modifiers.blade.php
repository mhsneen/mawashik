@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Products') }} - {{ __('dashboard.Product_Modifiers') }}
@endsection

@section('css')

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / {{ __('dashboard.Edit') }} / {{ __('dashboard.Product_Modifiers') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('products') }}" class="btn btn-info btn-icon"><i
                    class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection


@section('content')
    @include('dashboard.products.steps', ['active' => 6, 'id' => $item->id])
    <div class="row row-sm">
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <p class="text-muted">{{ __('dashboard.Modifiers_Page_Hint') }}</p>

            <form method="POST" action="{{ route('products.updateModifiers', $item) }}">
                @csrf

                @foreach ($modifierGroups as $attributeId => $values)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $values->first()->attribute->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.Value') }}</th>
                                            <th>{{ __('dashboard.Additional_Price') }}</th>
                                            <th>{{ __('dashboard.Quantity') }} <small class="text-muted">({{ __('dashboard.Unlimited') }})</small></th>
                                            <th class="text-center">{{ __('dashboard.Default') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($values as $value)
                                            <tr>
                                                <td>{{ $value->value }}</td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control"
                                                        name="modifiers[{{ $value->id }}][price]"
                                                        value="{{ $value->pivot->price ?? 0 }}">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" class="form-control"
                                                        name="modifiers[{{ $value->id }}][quantity]"
                                                        placeholder="{{ __('dashboard.Unlimited') }}"
                                                        value="{{ $value->pivot->quantity }}">
                                                </td>
                                                <td class="text-center">
                                                    <input type="hidden" name="modifiers[{{ $value->id }}][is_default]" value="0">
                                                    <input type="checkbox" name="modifiers[{{ $value->id }}][is_default]" value="1"
                                                        {{ $value->pivot->is_default ? 'checked' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card-footer text-center">
                    <button class="btn btn-success-gradient mg-10" type="submit">
                        {{ __('dashboard.Save') }} <i class="typcn typcn-clipboard"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
