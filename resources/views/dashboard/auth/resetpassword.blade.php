@extends('layouts.admin-login')
@section('title')
{{ __('passwords.resetpage') }}
@endsection

@section('css')
<!-- Sidemenu-respoansive-tabs css -->
<link href="{{URL::asset('assets/dashboard/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{URL::asset('vendor/mckenziearts/laravel-notify/css/'.getNotiftcss().'.css')}}">

@endsection
@section('content')
<div class="container-fluid">
	<div class="row no-gutter">
		<!-- The image half -->
		<div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
			<div class="row wd-100p mx-auto text-center">
				<div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
					<img src="{{URL::asset('assets/dashboard/img/media/forgot.png')}}" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
				</div>
			</div>
		</div>
		<!-- The content half -->
		<div class="col-md-6 col-lg-6 col-xl-5 bg-white">
			<div class="login d-flex align-items-center py-2">
				<!-- Demo content-->
				<div class="container p-0">
					<div class="row">
						<div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
							<div class="mb-5 d-flex"> <img src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}" class="sign-favicon ht-40" alt="logo"></div>
							<div class="main-card-signin d-md-flex bg-white">
								<div class="wd-100p">
									<div class="main-signin-header">
										<h2>{{ __('passwords.resetpage') }}</h2>
										<form method="POST" action="{{ route('admin.reset-password.update') }}" data-parsley-validate="">
											@csrf
											<input type="hidden" name="token" value="{{ $token }}">

											<div class="form-group">
												<label>{{ __('dashboard.Email') }}</label> <input class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('dashboard.Enter_your_email') }}" type="text" name="email" id="email"  required="" data-parsley-type="email" value="{{ $email ?? old('email') }}" autocomplete="email" autofocus>
												@error('email')
												<span class="tx-danger">{{ $message }}</span>
												@enderror
											</div>

											<div class="form-group">
												<label>{{ __('dashboard.Password') }}</label> <span class="tx-danger">*</span> <input class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('dashboard.Enter_your_password') }}" type="password"  name="password" id="password" required="" autocomplete="new-password">

												@error('password')
												<span class="tx-danger">{{ $message }}</span>
												@enderror

											</div>



											<div class="form-group">
												<label>{{ __('dashboard.Confirm_Password') }}</label> <span class="tx-danger">*</span> <input class="form-control" placeholder="{{ __('dashboard.Confirm_Password') }}" type="password"  name="password_confirmation" id="password_confirmation" required="" autocomplete="new-password">

												@error('password_confirmation')
												<span class="tx-danger">{{ $message }}</span>
												@enderror

											</div>


											<button class="btn btn-main-primary btn-block">{{ __('dashboard.Send') }}</button>
										</form>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div><!-- End -->
			</div>
		</div><!-- End -->
	</div>
</div>
@endsection
@section('js')
<!--Internal  Parsley.min js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/i18n/'. app()->getLocale() .'.js')}}"></script>
<!-- Internal Form-validation js -->
<script src="{{URL::asset('assets/dashboard/js/form-validation-login.js')}}"></script>
<x:notify-messages />
@notifyJs
@endsection