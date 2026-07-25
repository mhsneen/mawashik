@extends('layouts.admin_dashboard')
@section('title')
{{ $textpage->name }} - {{ __('dashboard.Edit') }}
@endsection
@section('css')

@endsection
@section('page-header')
				<!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ $textpage->name }} </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
						</div>
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
					<form class="form-horizontal" method="POST"  data-parsley-validate="" enctype="multipart/form-data" action ="{{ route('textpages.updatetext',$textpage->id) }}">
						@csrf

<input type="hidden" id="id" name="id" value="{{ $textpage->id }}"/>




					<div class="col-md-12">
						<div class="form-group ">
							<div class="row">
								<div class="col-md-3">
									<label class="form-label pd-t-10">{{ __('dashboard.Page_text') }} ( AR ) : </label>

								</div>
								<div class="col-md-8">
									<textarea class="form-control" id="details_ar" name="details_ar"> @if (!empty($textpage->translate('ar')->details)) {{$textpage->translate('ar')->details}}  @endif</textarea>

									@error('details_ar')
									<span class="tx-danger">{{ $message }} </span>
									@enderror
								</div>
							</div>
						</div>
					</div>










					<div class="col-md-12">
						<div class="form-group ">
							<div class="row">
								<div class="col-md-3">
									<label class="form-label pd-t-10">{{ __('dashboard.Page_text') }} ( EN ) : </label>

								</div>
								<div class="col-md-8">
									<textarea class="form-control" id="details_en" name="details_en"> @if (!empty($textpage->translate('en')->details)) {{$textpage->translate('en')->details}}  @endif</textarea>

									@error('details_en')
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


<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>



<script>
	CKEDITOR.replace( 'details_ar', {
		language:'{{ app()->getLocale() }}',
		filebrowserUploadMethod : 'form',
		filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
		filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

	});


	CKEDITOR.replace( 'details_en', {
		language:'{{ app()->getLocale() }}',
		filebrowserUploadMethod : 'form',
		filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
		filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

	});
	</script>


@include('ckfinder::setup')



@endsection