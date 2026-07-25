@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Edit') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">


    <!---Internal Fileupload css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet"
        type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
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
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i
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
                                    <input class="form-control tx_R" id="name_ar" name="name_ar"
                                        placeholder="{{ __('dashboard.Name') }} (AR) " required="" type="text"
                                        @if (isset($item->translate('ar')->name)) value="{{ $item->translate('ar')->name }}" @endif>
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
                                        @if (isset($item->translate('en')->name)) value="{{ $item->translate('en')->name }}" @endif>
                                    @error('name_en')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Image') }} <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <input type="file" class="dropify" name="image" id="image"
                                        data-parsley-errors-container="#slErrorContainerimage"
                                        data-default-file="{{ URL::asset('uploads/' . $folder . '/thumbnail/' . $item->image . '') }}" />

                                    <div id="slErrorContainerimage">
                                        @error('image')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>








                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('dashboard.Efficiency_Status') }}: <span
                                            class="tx-danger">*</span></label>

                                </div>
                                <div class="col-md-5">
                                    <div class="parsley-select " id="slWrapperis_active">
                                        <select class="form-control select2 "
                                            data-parsley-class-handler="#slWrapperis_active"
                                            data-parsley-errors-container="#slErrorContaineris_active"
                                            data-placeholder="{{ __('dashboard.Choose') }}" id="is_active" name="is_active"
                                            required="">
                                            <option value="  ">
                                                {{ __('dashboard.Choose') }}
                                            </option>
                                            <option value="1" {{ $item->is_active == true ? 'selected' : '' }}>
                                                {{ __('dashboard.effective') }}
                                            </option>
                                            <option value="0" {{ $item->is_active == false ? 'selected' : '' }}>
                                                {{ __('dashboard.Inactive') }}
                                            </option>

                                        </select>
                                        <div id="slErrorContaineris_active">
                                            @error('is_active')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>



    <!--Internal Fileuploads js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/' . app()->getLocale() . '/file-upload.js') }}">
    </script>
    <!--Internal Fancy uploader js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js') }}"></script>
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
