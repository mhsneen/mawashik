@extends('layouts.admin_dashboard')
@section('title')
{{ __('dashboard.Edit_Profile') }}
@endsection
@section('css')
<!---Internal Fileupload css-->
<link href="{{URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
<!---Internal Fancy uploader css-->
<link href="{{URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
				<!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ __('dashboard.Edit_Profile') }}</h4>
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
<!-- row -->
<div class="row row-sm">


	<!-- Col -->
	<div class="col-lg-12">
		@include('dashboard.includes.alerts.errors')
			@include('dashboard.includes.alerts.success')
		<div class="card">
			<div class="card-body">
				<form method="post"  data-parsley-validate="" enctype="multipart/form-data" action="{{ route('update.profile') }}">
					@csrf
					@method('PUT')
					<input type="hidden" id="id" name="id" value="{{ $admin->id }}">

					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.Name') }}: <span class="tx-danger">*</span></label>

							</div>
							<div class="col-md-5">
								<input class="form-control" id="name" name="name"  required="" type="text" value="{{ $admin->name  }}">
								@error('name')
								<span class="tx-danger">{{ $message }} </span>
								@enderror
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.Email') }}: <span class="tx-danger">*</span></label>

							</div>
							<div class="col-md-5">
								<input class="form-control" id="email" name="email"   data-parsley-type="email" type="text" value="{{ $admin->email  }}">
								@error('email')
								<span class="tx-danger">{{ $message }}</span>
								@enderror
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.Mobile') }}: <span class="tx-danger">*</span></label>

							</div>
							<div class="col-md-5">
								<input class="form-control" id="mobile" name="mobile"  required="" data-parsley-type="digits" type="text" value="{{ old('mobile', preg_replace('/^\+966/', '0', $admin->getRawOriginal('mobile') ?? '')) }}">
								@error('mobile')
								<span class="tx-danger">{{ $message }}</span>
								@enderror
							</div>
						</div>
					</div>






					<div class="form-group ">
						<div class="row">
							<div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.Image') }}: <span class="tx-danger">*</span></label>

							</div>
							<div class="col-md-5">
								<input type="file" class="dropify" data-height="200" name="image" id="image"  data-max-file-size="5M"   data-parsley-errors-container="#slErrorContainerimage" data-default-file="{{URL::asset('uploads/users/'.$admin->image.'')}}" />

								<div id="slErrorContainerimage">
									@error('image')
								<span class="tx-danger">{{ $message }} </span>
								@enderror
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
	<!-- /Col -->
</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->

@endsection
@section('js')
<!--Internal  Parsley.min js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js')}}"></script>
<!-- Internal Form-validation js -->
<script src="{{URL::asset('assets/dashboard/plugins/parsleyjs/i18n/'. app()->getLocale() .'.js')}}"></script>
<!--Internal Fileuploads js-->
<script src="{{URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fileuploads/js/'. app()->getLocale() .'/file-upload.js')}}"></script>
<!--Internal Fancy uploader js-->
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js')}}"></script>
@endsection
