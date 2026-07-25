@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Add_New') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> {{ __($namesection) }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Add_New') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>

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
                        action ="{{ route($routeName . '.store') }}">
                        @csrf

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Name') }} ( AR ) : <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <input class="form-control tx_R" id="name_ar" name="name_ar"
                                        placeholder="{{ __('dashboard.Name') }} (AR) " required="" type="text"
                                        value="{{ old('name_ar') }}">
                                    @error('name_ar')
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
                                    <input class="form-control tx_L" id="name_en" name="name_en"
                                        placeholder="{{ __('dashboard.Name') }} (EN) " required="" type="text"
                                        value="{{ old('name_en') }}">
                                    @error('name_en')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>









                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Property type') }}: <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <div class="parsley-select " id="slWrappertype">
                                        <select class="form-control select2 " data-parsley-class-handler="#slWrappertype"
                                            data-parsley-errors-container="#slErrorContainertype"
                                            data-placeholder="{{ __('dashboard.Choose') }}" id="type" name="type"
                                            required="">
                                            <option value="  ">
                                                {{ __('dashboard.Choose') }}
                                            </option>
                                            <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>
                                                {{ __('dashboard.Color') }}
                                            </option>
                                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>
                                                {{ __('dashboard.Values') }}
                                            </option>

                                        </select>
                                        <div id="slErrorContainertype">
                                            @error('type')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Attribute_Usage') }}: <span
                                            class="tx-danger">*</span></label>
                                </div>
                                <div class="col-md-5">
                                    <div class="parsley-select " id="slWrapperis_variation">
                                        <select class="form-control select2 " data-parsley-class-handler="#slWrapperis_variation"
                                            data-parsley-errors-container="#slErrorContaineris_variation"
                                            data-placeholder="{{ __('dashboard.Choose') }}" id="is_variation" name="is_variation"
                                            required="">
                                            <option value="">
                                                {{ __('dashboard.Choose') }}
                                            </option>
                                            <option value="1" {{ old('is_variation') == '1' ? 'selected' : '' }}>
                                                {{ __('dashboard.Variation_Usage') }}
                                            </option>
                                            <option value="0" {{ old('is_variation') == '0' ? 'selected' : '' }}>
                                                {{ __('dashboard.Modifier_Usage') }}
                                            </option>
                                        </select>
                                        <small class="text-muted d-block mt-1">{{ __('dashboard.Attribute_Usage_Hint') }}</small>
                                        <div id="slErrorContaineris_variation">
                                            @error('is_variation')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>





                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Save') }} <i
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


    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>


    <script>
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
