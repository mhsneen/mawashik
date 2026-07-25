@extends('layouts.admin-forgetMessage')
@section('title')
{{ __('dashboard.Forgot_Password_Page') }}
@endsection

@section('css')


@endsection
@section('content')
		<!-- Main-error-wrapper -->
		<div class="main-error-wrapper  page page-h " style="text-align:center">
			<img src="{{URL::asset('assets/dashboard/img/media/login.png')}}" class="error-page" alt="error">
            <h2>{{ __('dashboard.Verify_Your_Email_Address') }}</h2>
            <div class="card-body">
             @if (session('resent'))
                  <div class="alert alert-success" role="alert">
                    {{ __('dashboard.A_fresh_verification_link') }}
                 </div>
             @endif
            <h3> <a href="{{ route('admin.reset-password',$token) }}" style="color:red">{{ __('dashboard.Click_Here') }}</a></h3>
         </div>

        </div>
		<!-- /Main-error-wrapper -->
@endsection
@section('js')
@endsection