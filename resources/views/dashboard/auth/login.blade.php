@extends('layouts.admin-login')
@section('title')
{{ __('dashboard.Sign_In') }}
@endsection

@section('css')
<!-- Sidemenu-respoansive-tabs css -->
<link href="{{URL::asset('assets/dashboard/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{URL::asset('vendor/mckenziearts/laravel-notify/dist/'.getNotiftcss().'.css')}}">

@endsection
@section('content')
		<div class="container-fluid">
			<div class="row no-gutter">
				<!-- The image half -->
				<div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
					<div class="row wd-100p mx-auto text-center">
						<div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
							<img src="{{URL::asset('assets/dashboard/img/media/login.png')}}" class="my-auto ht-xl-80p wd-md-100p wd-xl-50p ht-xl-60p mx-auto" alt="logo">
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
									<div class="card-sigin">
										<div class="mb-5 d-flex"> <img src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}" class="sign-favicon " alt="logo"><h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">&nbsp;  </h1></div>
										<div class="card-sigin">
											<div class="main-signup-header">
												<h2>{{ __('dashboard.Welcome_back') }}</h2>
												<h5 class="font-weight-semibold mb-4">{{ __('dashboard.Please_sign_continue') }}</h5>

												@include('dashboard.includes.alerts.errors')
												@include('dashboard.includes.alerts.success')


<br/>
												<form action="{{ route('admin.postlogin') }}" method="POST" data-parsley-validate="">
                                                    @csrf
													<div class="form-group">
														<label>{{ __('dashboard.Email_OR_Mobile') }}</label> <span class="tx-danger">*</span> <input class="form-control" placeholder="{{ __('dashboard.Email_OR_Mobile') }}" type="text"  name="email" id="email" value="{{old('email')}}" required="">
														@error('email')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
													<div class="form-group">
														<label>{{ __('dashboard.Password') }}</label> <span class="tx-danger">*</span> <input class="form-control" placeholder="{{ __('dashboard.Enter_your_password') }}" type="password"  name="password" id="password" required="">

														@error('password')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>
                                                    <div class="parsley-checkbox" id="cbWrapper">
                                                        <label class="ckbox mg-b-5"><input  name="remeber_me" id="remeber_me" type="checkbox" ><span>{{ __('dashboard.Remember_me') }}</span></label>
                                                    </div>
                                                    <br/>
                                                    <button class="btn btn-main-primary btn-block">{{ __('dashboard.Sign_In') }}</button>

												</form>

												<div class="main-signin-footer mt-2">
													<p><a href="{{ route('admin.forgotpassword') }}">{{ __('dashboard.Forgot_password') }}</a></p>
                                                </div>
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



@notifyJs
@endsection
