@extends('layouts.admin_dashboard')
@section('title')
{{ __('dashboard.Edit_password') }}
@endsection
@section('css')

@endsection
@section('page-header')
				<!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ __('dashboard.Edit_password') }}</h4>
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
                <form method="post"  data-parsley-validate="" enctype="multipart/form-data" action="{{ route('update.password') }}">
                @csrf
				@method('PUT')
					<div class="row row-sm">
                        <div class="col-6">
                            <div class="form-group mg-b-0">
                                <label class="form-label">{{ __('dashboard.Old_Password') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control" id="oldpassword" name="oldpassword"  required="" type="password" >
								@error('oldpassword')
								<span class="tx-danger">{{ $message }} </span>
								@enderror
							</div>
                        </div>
						<div class="clearfix"><br/><br/><br/><br/><br/></div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('dashboard.New_Password') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control" id="password" name="password"  required=""  type="password" >
								@error('password')
								<span class="tx-danger">{{ $message }}</span>
								@enderror
							</div>
                        </div>


						<div class="col-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('dashboard.Confirm_Password') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control" id="password-confirmation" name="password-confirmation"  required=""  type="password" >
								@error('password-confirmation')
								<span class="tx-danger">{{ $message }}</span>
								@enderror
							</div>
                        </div>


						<div class="row row-sm wd-xl-80p">
							<button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Save') }} <i class="typcn typcn-clipboard"></i></button>
							{{-- <ahref="javascript:void(0)"class="btnbtn-secondary-gradientmg-10"onclick="history.back();">__('dashboard.Back')  <i class="typcn typcn-arrow-back-outline"></i></a> --}}
						
						</div>
						</div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
@section('js')
<!--Internal  Parsley.min js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js')}}"></script>
<!-- Internal Form-validation js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/i18n/'. app()->getLocale() .'.js')}}"></script>
@endsection