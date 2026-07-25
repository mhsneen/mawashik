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

    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __($namesection) }}</h4><span
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
                        action="{{ route($routeName . '.update', $item->id) }}">
                        @csrf

                        <input type="hidden" id="id" name="id" value="{{ $item->id }}" />



                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Name') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R" id="name" name="name"
                                                placeholder="{{ __('dashboard.Name') }}  " required="" type="text"
                                                value="{{ $item->name }}">
                                            @error('name')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

<div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Email') }} : </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R" id="email" name="email"
                                                placeholder="{{ __('dashboard.Email') }}  "  type="text"
                                                value="{{ $item->email }}" data-parsley-type="email">
                                            @error('email')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Efficiency_Status') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="row mg-t-10">
                                                <div class="col-lg-4">
                                                    <label class="rdiobox"><input name="is_active" id="is_active"
                                                            type="radio" value="1" required=""
                                                            @if ($item->is_active == '1') checked @endif>
                                                        <span>{{ __('dashboard.effective') }}</span></label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <label class="rdiobox"><input name="is_active" id="is_active"
                                                            type="radio" value="0" required=""
                                                            @if ($item->is_active == '0') checked @endif>
                                                        <span>{{ __('dashboard.Inactive') }}</span></label>
                                                </div>
                                            </div>
                                            @error('is_active')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>





                        </div>
                        <hr />
                        <br />
                        <div class="main-content-label mg-b-5"> {{ __('dashboard.Login_information_application') }}
                        </div><br />

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Mobile') }}: <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="mobile" name="mobile"
                                                placeholder="{{ __('dashboard.Mobile') }}  " required="" type="text"
                                                value="{{ $item->mobile }}" data-parsley-type="number" autocomplete="false"
                                                readonly onfocus="this.removeAttribute('readonly');">
                                            @error('mobile')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>





                        <hr />
                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Image') }}: </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="dropify" data-height="200" name="image"
                                                id="image" data-max-file-size="5M"
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

    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>


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


    <!--Internal  Sweet-Alert js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>


    <script type="text/javascript">
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
