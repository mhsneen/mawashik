@extends('layouts.errors')
@section('title')
{{ __('dashboard.USER_DOES_NOT_HAVE_THE_RIGHT_PERMISSIONS') }}
@endsection
@section('css')
<!--- Internal Fontawesome css-->
<link href="{{URL::asset('assets/dashboard/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!---Ionicons css-->
<link href="{{URL::asset('assets/dashboard/plugins/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
<!---Internal Typicons css-->
<link href="{{URL::asset('assets/dashboard/plugins/typicons.font/typicons.css')}}" rel="stylesheet">
<!---Internal Feather css-->
<link href="{{URL::asset('assets/dashboard/plugins/feather/feather.css')}}" rel="stylesheet">
<!---Internal Falg-icons css-->
<link href="{{URL::asset('assets/dashboard/plugins/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection
@section('content')
		<!-- Main-error-wrapper -->
		<div class="main-error-wrapper  page page-h ">
			<img src="{{URL::asset('assets/dashboard/img/media/403.png')}}" class="error-page" alt="error"><br/><br/>
			<h2>{{ __('dashboard.USER_DOES_NOT_HAVE_THE_RIGHT_PERMISSIONS') }}</h2>
			<h6>{{ __('dashboard.mistyped_address_page') }}</h6><a class="btn btn-outline-danger" href="{{ url()->previous() }}">{{ __('dashboard.back') }}</a>

		</div>
		<!-- /Main-error-wrapper -->
@endsection
@section('js')
@endsection
