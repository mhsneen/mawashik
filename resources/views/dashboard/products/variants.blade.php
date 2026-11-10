@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Products') }} - {{ __('dashboard.Product_Variants') }}
@endsection

@section('css')

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / {{ __('dashboard.Edit') }} / {{ __('dashboard.Product_Variants') }}</span>
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
    @include('dashboard.products.steps', ['active' => 5, 'id' => $item->id])
    <div class="row row-sm">
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card">
                <div class="card-body">

                    <p class="text-muted">{{ __('dashboard.Variants_Page_Hint') }}</p>

                    <form method="POST" action="{{ route('products.updateVariants', $item) }}">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.Combination') }}</th>
                                        <th>{{ __('dashboard.SKU') }}</th>
                                        <th>{{ __('dashboard.Price') }} <small class="text-muted">({{ __('dashboard.Optional') }})</small></th>
                                        <th>{{ __('dashboard.Quantity') }} <small class="text-muted">({{ __('dashboard.Optional') }})</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $i => $row)
                                        <tr>
                                            <td>
                                                {{ $row['label'] }}
                                                <input type="hidden" name="variants[{{ $i }}][value_ids]"
                                                    value="{{ implode(',', $row['value_ids']) }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    name="variants[{{ $i }}][sku]" value="{{ $row['sku'] }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    name="variants[{{ $i }}][price]"
                                                    placeholder="{{ __('dashboard.Default_Product_Price') }}"
                                                    value="{{ $row['price'] }}">
                                                @error('variants.' . $i . '.price')
                                                    <span class="tx-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" min="0" class="form-control"
                                                    name="variants[{{ $i }}][quantity]"
                                                    placeholder="{{ __('dashboard.Default_Product_Quantity') }}"
                                                    value="{{ $row['quantity'] }}">
                                                @error('variants.' . $i . '.quantity')
                                                    <span class="tx-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer text-center">
                            <button class="btn btn-success-gradient mg-10" type="submit">
                                {{ __('dashboard.Save') }} <i class="typcn typcn-clipboard"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
