@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Loyalty_Points') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Loyalty_Points') }}</h4><span
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
                        action="{{ route('update.updateloyaltypointssettings') }}">
                        @csrf


                        <div class="row row-xs formgroup-wrapper">
















                            <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label
                                                class="form-label">{{ __('dashboard.loyalty_points_enabled') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="parsley-select " id="slWrapperloyalty_points_enabled">
                                                <select class="form-control select2 "
                                                    data-parsley-class-handler="#slWrapperloyalty_points_enabled"
                                                    data-parsley-errors-container="#slErrorContainerloyalty_points_enabled"
                                                    data-placeholder="{{ __('dashboard.Choose') }}"
                                                    id="loyalty_points_enabled" name="loyalty_points_enabled" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>
                                                    <option value="1"
                                                        {{ $setting['loyalty_points_enabled']->plain_value == 1 ? 'selected' : '' }}>
                                                        {{ __('dashboard.Yes') }}
                                                    </option>
                                                    <option value="0"
                                                        {{ $setting['loyalty_points_enabled']->plain_value == 0 ? 'selected' : '' }}>
                                                        {{ __('dashboard.No') }}
                                                    </option>

                                                </select>
                                                <div id="slErrorContainerloyalty_points_enabled">
                                                    @error('loyalty_points_enabled')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>





    <div class="col-md-12 ">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.loyalty_points_earn_rate') }} : <span
                                                    class="tx-danger">*</span>
                                                </label>
<small>{{ __('dashboard.loyalty_points_earn_rate_hint') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="loyalty_points_earn_rate" name="loyalty_points_earn_rate"
                                                placeholder="{{ __('dashboard.loyalty_points_earn_rate') }}  " required=""
                                                type="text" value="{{ $setting['loyalty_points_earn_rate']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('loyalty_points_earn_rate')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.loyalty_points_redeem_value') }} : <span
                                                    class="tx-danger">*</span>
                                                </label>
<small>{{ __('dashboard.loyalty_points_redeem_value_hint') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="loyalty_points_redeem_value" name="loyalty_points_redeem_value"
                                                placeholder="{{ __('dashboard.loyalty_points_redeem_value') }}  " required=""
                                                type="text" value="{{ $setting['loyalty_points_redeem_value']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('loyalty_points_redeem_value')
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
                                            <label class="form-label pd-t-10">{{ __('dashboard.loyalty_points_min_redeem') }} : <span
                                                    class="tx-danger">*</span>
                                                </label>
<small>{{ __('dashboard.loyalty_points_min_redeem_hint') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control tx_L " id="loyalty_points_min_redeem" name="loyalty_points_min_redeem"
                                                placeholder="{{ __('dashboard.loyalty_points_min_redeem') }}  " required=""
                                                type="text" value="{{ $setting['loyalty_points_min_redeem']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('loyalty_points_min_redeem')
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

    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>







    <script>
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });


    </script>
@endsection
