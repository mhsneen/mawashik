@extends('layouts.admin_dashboard')
@section('title')
{{ __('dashboard.General_Settings') }}
@endsection
@section('css')
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
							<h4 class="content-title mb-0 my-auto">{{ __('dashboard.General_Settings') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
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
					</div>--}}
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
                <form method="post"  data-parsley-validate="" enctype="multipart/form-data" action="{{ route('update.generalsettings') }}">
                @csrf


				<div class="row row-xs formgroup-wrapper">
					<div class="col-md-12">
						<div class="form-group ">
							<div class="row">
                                <div class="col-md-4">
									<label class="form-label pd-t-10">{{ __('dashboard.App_Name') }} ( AR ) : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_R" id="store_name_ar" name="store_name_ar" placeholder="{{ __('dashboard.App_Name') }} ( AR ) " required=""  type="text"  value="@if(isset($setting['store_name']->translate('ar')->value)){{ $setting['store_name']->translate('ar')->value }}@endif"  >
									@error('store_name_ar')
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
									<label class="form-label pd-t-10">{{ __('dashboard.App_Name') }} ( EN ) : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_L " id="store_name_en" name="store_name_en" placeholder="{{ __('dashboard.App_Name') }} (EN) " required=""  type="text"   value="@if(isset($setting['store_name']->translate('en')->value)){{ $setting['store_name']->translate('en')->value }}@endif">
									@error('store_name_en')
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
                                        <label class="form-label pd-t-10">{{ __('dashboard.Logo') }} :   <span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" class="dropify"  name="store_logo"
                                            id="store_logo" data-max-file-size="5M"
                                            data-parsley-errors-container="#slErrorContainerimagestore_logo"
                                            data-default-file="{{ URL::asset('uploads/settings/thumbnail/' . $setting['store_logo']->plain_value . '') }}" />

                                        <div id="slErrorContainerimagestore_logo">
                                            @error('store_logo')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="col-md-12">
						<div class="form-group ">
							<div class="row">
                                <div class="col-md-4">
									<label class="form-label pd-t-10">{{ __('dashboard.Email') }}  : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_L" id="store_email" name="store_email" placeholder="{{ __('dashboard.Email') }} " required=""  type="text"  value="{{ $setting['store_email']->plain_value }}"  data-parsley-type="email">
									@error('store_email')
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
									<label class="form-label pd-t-10">{{ __('dashboard.Mobile') }}  : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_L " id="store_mobile" name="store_mobile" placeholder="{{ __('dashboard.Mobile') }}  " required=""  type="text"   value="{{ $setting['store_mobile']->plain_value  }}" data-parsley-type="number">
									@error('store_mobile')
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
									<label class="form-label pd-t-10">{{ __('dashboard.Whatsapp') }}  : <span class="tx-danger">*</span> </label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_L " id="store_whatsapp" name="store_whatsapp" placeholder="{{ __('dashboard.Whatsapp') }}  "   type="tel"    value="{{ $setting['store_whatsapp']->plain_value  }}" data-parsley-pattern="^\+?[0-9]{8,15}$"
    data-parsley-pattern-message="يرجى إدخال رقم واتساب صحيح (من 8 إلى 15 رقم)" required="">
									@error('store_whatsapp')
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
									<label class="form-label pd-t-10">{{ __('dashboard.Address') }} ( AR ) : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_R" id="store_address_ar" name="store_address_ar" placeholder="{{ __('dashboard.Address') }} ( AR ) " required=""  type="text"  value="@if(isset($setting['store_address']->translate('ar')->value)){{ $setting['store_address']->translate('ar')->value }}@endif"  >
									@error('store_address_ar')
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
									<label class="form-label pd-t-10">{{ __('dashboard.Address') }} ( EN ) : <span class="tx-danger">*</span></label>

								</div>
								<div class="col-md-6">
									<input class="form-control tx_L " id="store_address_en" name="store_address_en" placeholder="{{ __('dashboard.Address') }} (EN) " required=""  type="text"   value="@if(isset($setting['store_address']->translate('en')->value)){{ $setting['store_address']->translate('en')->value }}@endif">
									@error('store_address_en')
									<span class="tx-danger">{{ $message }} </span>
									@enderror
								</div>
							</div>
						</div>
					</div>


				</div>
					<div class="card-footer text-center">

						<button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Edit') }} <i class="typcn typcn-clipboard"></i></button>
					</div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
@section('js')

<!--Internal Fileuploads js-->
<script src="{{URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fileuploads/js/'. app()->getLocale() .'/file-upload.js')}}"></script>
<!--Internal Fancy uploader js-->
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js')}}"></script>
<!--Internal  Parsley.min js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js')}}"></script>
<!-- Internal Form-validation js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/i18n/'. app()->getLocale() .'.js')}}"></script>

@endsection
