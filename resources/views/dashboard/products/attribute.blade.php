@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Products') }} - {{ __('dashboard.Edit') }} - {{ __('dashboard.Product_Features') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / {{ __('dashboard.Edit') }} / {{ __('dashboard.Product_Features') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('products') }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    @include('dashboard.products.steps', ['active' => 4, 'id' => $item->id])
    <!-- row -->
    <div class="row row-sm">




        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')


            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route('products.updateattribute', $item) }}">
                        @csrf






                        <div class="row row-xs formgroup-wrapper">
                            @foreach ($attributes as $attribute)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <!-- اسم الخاصية -->
                                            <div class="col-md-2">
                                                <label class="form-label pd-t-10">{{ $attribute->name }}: </label>
                                            </div>

                                            <!-- قيم الخاصية -->
                                            <div class="col-md-9">
                                                <div class="parsley-select" id="slWrapperattribute_{{ $attribute->id }}">
                                                    <select class="form-control select2"
                                                        name="attribute_values[{{ $attribute->id }}][]" multiple="multiple"
                                                        data-parsley-class-handler="#slWrapperattribute_{{ $attribute->id }}"
                                                        data-parsley-errors-container="#slErrorContainerattribute_{{ $attribute->id }}"
                                                        data-placeholder="{{ __('dashboard.Choose') }}"
                                                        style="width: 100%;">
                                                        @foreach ($attribute->values as $value)
                                                            <option value="{{ $value->id }}"
                                                                {{ isset($selectedValues[$attribute->id]) && in_array($value->id, $selectedValues[$attribute->id]) ? 'selected' : '' }}>
                                                                {{ $value->value }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <!-- رسائل الأخطاء -->
                                                    <div id="slErrorContainerattribute_{{ $attribute->id }}">
                                                        @error('attribute_values.' . $attribute->id)
                                                            <span class="tx-danger">{{ $message }}</span>
                                                        @enderror

                                                        @error('attribute_values.' . $attribute->id . '.0')
                                                            <span
                                                                class="tx-danger">{{ $errors->first('attribute_values.' . $attribute->id . '.0') }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div> <!-- col-md-9 -->
                                        </div> <!-- row -->
                                    </div> <!-- form-group -->
                                </div> <!-- col-md-6 -->
                            @endforeach
                        </div>


















                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Edit') }} <i
                                    class="typcn typcn-clipboard"></i></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- /Col -->
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>




    <script>
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
