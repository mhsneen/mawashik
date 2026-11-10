@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Edit') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Internal Spectrum-colorpicker css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __($namesection) }} </h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName, $item->attribute_id) }}" class="btn btn-info btn-icon"><i
                    class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">


        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName . '.update', $item->id) }}">
                        @csrf

                        <input type="hidden" id="id" name="id" value="{{ $item->id }}" />


                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Name') }} ( AR ) : <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <input class="form-control tx_R" id="value_ar" name="value_ar"
                                        placeholder="{{ __('dashboard.Name') }} (AR) " required="" type="text"
                                        @if (isset($item->translate('ar')->value)) value="{{ $item->translate('ar')->value }}" @endif>
                                    @error('value_ar')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Name') }} ( EN ) : <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <input class="form-control tx_L" id="value_en" name="value_en"
                                        placeholder="{{ __('dashboard.Name') }} (EN) " required="" type="text"
                                        @if (isset($item->translate('en')->value)) value="{{ $item->translate('en')->value }}" @endif>
                                    @error('value_en')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        @if ($attribute->type == 'color')
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('dashboard.Color code') }} : <span
                                                class="tx-danger">*</span></label>

                                    </div>
                                    <div class="col-md-5">
                                        <input class="form-control tx_L" id="colorpicker" name="color_code"
                                            placeholder="{{ __('dashboard.Color code') }} " required="" type="text"
                                            value="{{ $item->color_code }}">
                                        @error('color_code')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif












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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>



    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
    <script>
        $('#colorpicker').spectrum({
            preferredFormat: "hex",
            showInput: true,
            allowEmpty: false
        });
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
