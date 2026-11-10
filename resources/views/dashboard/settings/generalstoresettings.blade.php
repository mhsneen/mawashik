@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Store_Settings') }}
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
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Store_Settings') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
            </div>
        </div>
        {{--  	<div class="d-flex my-xl-auto right-content">
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-filter-variant"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-danger btn-icon ml-2"><i class="mdi mdi-star"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-refresh"></i></button>
						</div>
						<div class="mb-3 mb-xl-0">
							<div class="btn-group dropdown">
								<button type="button" class="btn btn-primary">14 Aug 2019</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuDate" data-x-placement="bottom-end">
									<a class="dropdown-item" href="#">2015</a>
									<a class="dropdown-item" href="#">2016</a>
									<a class="dropdown-item" href="#">2017</a>
									<a class="dropdown-item" href="#">2018</a>
								</div>
							</div>
						</div>
					</div> --}}
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <div class="row">


        <div class="col-lg-12 col-md-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">

                <div class="card-body">
                    {{--   <div class="main-content-label mg-b-5">
                    Required Input Validation
                </div>
                <p class="mg-b-20">It is Very Easy to Customize and it uses in your website apllication.</p> --}}
                    <form method="post" data-parsley-validate="" enctype="multipart/form-data"
                        action="{{ route('update.updatestoregeneralsettings') }}">
                        @csrf


                        <div class="row row-xs formgroup-wrapper">


                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.added_tax') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="added_tax" name="added_tax"
                                                placeholder="{{ __('dashboard.added_tax') }}  " required=""
                                                type="text" value="{{ $setting['added_tax']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('added_tax')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

<!--


                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.minimum_price_order') }} :
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="minimum_price_order"
                                                name="minimum_price_order"
                                                placeholder="{{ __('dashboard.minimum_price_order') }}  " required=""
                                                type="text" value="{{ $setting['minimum_price_order']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('minimum_price_order')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

-->



                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.expired_quantities') }} :
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control  " id="expired_quantities" required=""
                                                name="expired_quantities"
                                                placeholder="{{ __('dashboard.delivery_per_kilo') }}  " type="text"
                                                value="{{ $setting['expired_quantities']->plain_value }}"
                                                data-parsley-type="integer" min="0">
                                            @error('expired_quantities')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.currentVersionNumber') }} :
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="currentVersionNumber"
                                                name="currentVersionNumber"
                                                placeholder="{{ __('dashboard.currentVersionNumber') }}  " required=""
                                                type="text" value="{{ $setting['currentVersionNumber']->plain_value }}">
                                            @error('currentVersionNumber')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.AppStore') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="appStorePageLink" name="appStorePageLink"
                                                placeholder="{{ __('dashboard.AppStore') }}  " type="text"
                                                value="{{ $setting['appStorePageLink']->plain_value }}"
                                                data-parsley-type="url">
                                            @error('appStorePageLink')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Googleaplay') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="googlePlayPageLink"
                                                name="googlePlayPageLink"
                                                placeholder="{{ __('dashboard.Googleaplay') }}  " type="text"
                                                value="{{ $setting['googlePlayPageLink']->plain_value }}"
                                                data-parsley-type="url">
                                            @error('googlePlayPageLink')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>









                            <!--
                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label
                                                class="form-label">{{ __('dashboard.The application is undergoing maintenance') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="parsley-select " id="slWrapperappClosed">
                                                <select class="form-control select2 "
                                                    data-parsley-class-handler="#slWrapperappClosed"
                                                    data-parsley-errors-container="#slErrorContainerappClosed"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="appClosed"
                                                    name="appClosed" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>
                                                    <option value="1"
                                                        {{ $setting['appClosed']->plain_value == 1 ? 'selected' : '' }}>
                                                        {{ __('dashboard.Yes') }}
                                                    </option>
                                                    <option value="0"
                                                        {{ $setting['appClosed']->plain_value == 0 ? 'selected' : '' }}>
                                                        {{ __('dashboard.No') }}
                                                    </option>

                                                </select>
                                                <div id="slErrorContainerappClosed">
                                                    @error('appClosed')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Maintenance text') }} ( AR
                                                )
                                                : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="appClosedText_ar" name="appClosedText_ar">
@if (isset($setting['appClosedText']->translate('ar')->value))
{{ $setting['appClosedText']->translate('ar')->value }}
@endif
</textarea>

                                            @error('appClosedText_ar')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Maintenance text') }} ( EN
                                                )
                                                : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="appClosedText_en" name="appClosedText_en">
@if (isset($setting['appClosedText']->translate('en')->value))
{{ $setting['appClosedText']->translate('en')->value }}
@endif
</textarea>

                                            @error('appClosedText_en')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('dashboard.Fullscreen advertisement') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="parsley-select " id="slWrappershowFullScreenAdvert">
                                                <select class="form-control select2 "
                                                    data-parsley-class-handler="#slWrappershowFullScreenAdvert"
                                                    data-parsley-errors-container="#slErrorContainershowFullScreenAdvert"
                                                    data-placeholder="{{ __('dashboard.Choose') }}"
                                                    id="showFullScreenAdvert" name="showFullScreenAdvert" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>
                                                    <option value="1"
                                                        {{ $setting['showFullScreenAdvert']->plain_value == 1 ? 'selected' : '' }}>
                                                        {{ __('dashboard.Yes') }}
                                                    </option>
                                                    <option value="0"
                                                        {{ $setting['showFullScreenAdvert']->plain_value == 0 ? 'selected' : '' }}>
                                                        {{ __('dashboard.No') }}
                                                    </option>

                                                </select>
                                                <div id="slErrorContainershowFullScreenAdvert">
                                                    @error('showFullScreenAdvert')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Adimage') }} : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="file" class="dropify" name="bannerLink" id="bannerLink"
                                                data-max-file-size="5M"
                                                data-parsley-errors-container="#slErrorContainerimagebannerLink"
                                                data-default-file="{{ URL::asset('uploads/settings/thumbnail/' . $setting['bannerLink']->plain_value . '') }}" />

                                            <div id="slErrorContainerimagebannerLink">
                                                @error('bannerLink')
                                                    <span class="tx-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        -->






                        </div>
                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Edit') }} <i
                                    class="typcn typcn-clipboard"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('js')
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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>






    <script>
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });

        CKEDITOR.replace('appClosedText_ar', {
            language: 'ar',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });

        CKEDITOR.replace('appClosedText_en', {
            language: 'en',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });
    </script>
    @include('ckfinder::setup')
@endsection
