@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Contact_Us') }} - {{ __('dashboard.View') }}
@endsection
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Contact_Us') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">

        <div class="col-sm-12 col-lg-12 col-xl-12">
            <div class="">
                <a class="main-header-arrow" href="" id="ChatBodyHide"><i class="icon ion-md-arrow-back"></i></a>
                <div class="main-content-body main-content-body-contacts card custom-card">
                    <div class="main-contact-info-header pt-3">
                        <div class="media">

                            <div class="media-body">
                                <h5>{{ $item->name }}</h5>
                                <h5>{{ $item->email }}</h5>
                                <h5>{{ $item->mobile }}</h5>
                                <span>{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="main-contact-info-body p-4">
                        <div>

                            <h6>{{ __('dashboard.Subject') }} : {{ $item->subject }}</h6>
                            <br /><br />
                            <h6>{{ __('dashboard.popup_text') }}</h6>
                            <p>{{ $item->details }}</p>

                        </div>
                        <div class="media-list pb-0">
                            <div class="media">
                                <div class="media-body">
                                    {{--
                        							<div>
													<label>{{ __('dashboard.Mobile') }}</label> <span class="tx-medium">{{ $item->mobile }}</span>

												</div>
                        --}}

                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{ URL::asset('assets/dashboard/js/contact.js') }}"></script>
@endsection
