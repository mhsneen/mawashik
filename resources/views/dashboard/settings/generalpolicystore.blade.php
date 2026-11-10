@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Store_policy_settings') }}
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
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Store_policy_settings') }}</h4><span
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
                        action="{{ route('update.updategeneralpolicystore') }}">
                        @csrf


                        <div class="row row-xs formgroup-wrapper">

                            <div class="main-content-label mg-b-5"> {{ __('dashboard.Free shipping') }} </div><br /><br />
                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Free shipping') }}
                                                ( AR ) :</label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_R " id="free_shipping_sentence_ar"
                                                name="free_shipping_sentence_ar"
                                                placeholder="{{ __('dashboard.sentence') }} : {{ __('dashboard.Free shipping') }}    ( AR )  "
                                                type="text"
                                                value="@if (isset($setting['free_shipping_sentence']->translate('ar')->value)) {{ $setting['free_shipping_sentence']->translate('ar')->value }} @endif">
                                            @error('free_shipping_sentence_ar')
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
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Free shipping') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="free_shipping_sentence_en"
                                                name="free_shipping_sentence_en"
                                                placeholder=" {{ __('dashboard.sentence') }} : {{ __('dashboard.Free shipping') }}    ( EN )  "
                                                type="text"
                                                value="@if (isset($setting['free_shipping_sentence']->translate('en')->value)) {{ $setting['free_shipping_sentence']->translate('en')->value }} @endif">
                                            @error('free_shipping_sentence_en')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Free shipping') }}
                                                ( AR ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="free_shipping_details_ar" name="free_shipping_details_ar">
@if (isset($setting['free_shipping_details']->translate('ar')->value))
{{ $setting['free_shipping_details']->translate('ar')->value }}
@endif
</textarea>

                                            @error('free_shipping_details_ar')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Free shipping') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="free_shipping_details_en" name="free_shipping_details_en">
@if (isset($setting['free_shipping_details']->translate('en')->value))
{{ $setting['free_shipping_details']->translate('en')->value }}
@endif
</textarea>

                                            @error('free_shipping_details_en')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <hr />
                            <br />
                            <div class="main-content-label mg-b-5"> {{ __('dashboard.Return policy') }} </div><br /><br />









                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Return policy') }}
                                                ( AR ) :</label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_R " id="return_policy_sentence_ar"
                                                name="return_policy_sentence_ar"
                                                placeholder="{{ __('dashboard.sentence') }} : {{ __('dashboard.Return policy') }}    ( AR )  "
                                                type="text"
                                                value="@if (isset($setting['return_policy_sentence']->translate('ar')->value)) {{ $setting['return_policy_sentence']->translate('ar')->value }} @endif">
                                            @error('return_policy_sentence_ar')
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
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Return policy') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="return_policy_sentence_en"
                                                name="return_policy_sentence_en"
                                                placeholder=" {{ __('dashboard.sentence') }} : {{ __('dashboard.Return policy') }}    ( EN )  "
                                                type="text"
                                                value="@if (isset($setting['return_policy_sentence']->translate('en')->value)) {{ $setting['return_policy_sentence']->translate('en')->value }} @endif">
                                            @error('return_policy_sentence_en')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Return policy') }}
                                                ( AR ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="return_policy_details_ar" name="return_policy_details_ar">
@if (isset($setting['return_policy_details']->translate('ar')->value))
{{ $setting['return_policy_details']->translate('ar')->value }}
@endif
</textarea>

                                            @error('return_policy_details_ar')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Return policy') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="return_policy_details_en" name="return_policy_details_en">
@if (isset($setting['return_policy_details']->translate('en')->value))
{{ $setting['return_policy_details']->translate('en')->value }}
@endif
</textarea>

                                            @error('return_policy_details_en')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>









                            <hr />
                            <br />
                            <div class="main-content-label mg-b-5"> {{ __('dashboard.Secure payment') }} </div>
                            <br /><br />




                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Secure payment') }}
                                                ( AR ) :</label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_R " id="secure_payment_sentence_ar"
                                                name="secure_payment_sentence_ar"
                                                placeholder="{{ __('dashboard.sentence') }} : {{ __('dashboard.Secure payment') }}    ( AR )  "
                                                type="text"
                                                value="@if (isset($setting['secure_payment_sentence']->translate('ar')->value)) {{ $setting['secure_payment_sentence']->translate('ar')->value }} @endif">
                                            @error('secure_payment_sentence_ar')
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
                                            <label class="form-label pd-t-10"> {{ __('dashboard.sentence') }} :
                                                {{ __('dashboard.Secure payment') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="secure_payment_sentence_en"
                                                name="secure_payment_sentence_en"
                                                placeholder=" {{ __('dashboard.sentence') }} : {{ __('dashboard.Secure payment') }}    ( EN )  "
                                                type="text"
                                                value="@if (isset($setting['secure_payment_sentence']->translate('en')->value)) {{ $setting['secure_payment_sentence']->translate('en')->value }} @endif">
                                            @error('secure_payment_sentence_en')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Secure payment') }}
                                                ( AR ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="secure_payment_details_ar" name="secure_payment_details_ar">
@if (isset($setting['secure_payment_details']->translate('ar')->value))
{{ $setting['secure_payment_details']->translate('ar')->value }}
@endif
</textarea>

                                            @error('secure_payment_details_ar')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.details') }} :
                                                {{ __('dashboard.Secure payment') }}
                                                ( EN ) : </label>

                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="secure_payment_details_en" name="secure_payment_details_en">
@if (isset($setting['secure_payment_details']->translate('en')->value))
{{ $setting['secure_payment_details']->translate('en')->value }}
@endif
</textarea>

                                            @error('secure_payment_details_en')
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

        CKEDITOR.replace('free_shipping_details_ar', {
            language: 'ar',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });

        CKEDITOR.replace('free_shipping_details_en', {
            language: 'en',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });

        CKEDITOR.replace('return_policy_details_ar', {
            language: 'ar',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });

        CKEDITOR.replace('return_policy_details_en', {
            language: 'en',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });


        CKEDITOR.replace('secure_payment_details_ar', {
            language: 'ar',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });

        CKEDITOR.replace('secure_payment_details_en', {
            language: 'en',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });
    </script>
    @include('ckfinder::setup')
@endsection
