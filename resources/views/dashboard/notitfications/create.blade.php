@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Alerts') }} - {{ __('dashboard.Send_alerts_to_users') }}
@endsection
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2totree.css') }}" rel="stylesheet">

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
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Alerts') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Send_alerts_to_users') }}</span>
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
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route('notifications.store') }}">
                        @csrf


                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('dashboard.Receiver') }}: <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="parsley-select " id="slWrapperis_recivers">
                                                <select class="form-control select2"
                                                    data-parsley-class-handler="#slWrapperis_recivers"
                                                    data-parsley-errors-container="#slErrorContaineris_recivers"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="recivers"
                                                    name="recivers" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>




                                                    <option value="all" {{ old('recivers') == 'all' ? 'selected' : '' }}>
                                                        {{ __('dashboard.All') }}
                                                    </option>

                                                    <option value="all_clients"
                                                        {{ old('recivers') == 'all_clients' ? 'selected' : '' }}>
                                                        {{ __('dashboard.All_clients') }}
                                                    </option>

                                                    <option value="all_drivers"
                                                        {{ old('recivers') == 'all_drivers' ? 'selected' : '' }}>
                                                        {{ __('dashboard.All_drivers') }}
                                                    </option>



                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ old('recivers') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach

                                                        @foreach ($drivers as $driver)
                                                        <option value="{{ $driver->id }}"
                                                            {{ old('recivers') == $driver->id ? 'selected' : '' }}>
                                                            {{ $driver->name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                                <div id="slErrorContaineris_recivers">
                                                    @error('recivers')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Title') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="msg_title" name="msg_title"
                                                placeholder="{{ __('dashboard.Title') }}  " required="" type="text"
                                                value="{{ old('msg_title') }}">
                                            @error('msg_title')
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
                                    <label class="form-label pd-t-10">{{ __('dashboard.Message_text') }} : <span
                                            class="tx-danger">*</span></label>

                                    <textarea class="form-control " placeholder="{{ __('dashboard.Message_text') }} " id="msg_details" name="msg_details"
                                        style="height: 120px" required=""> {{ old('msg_details') }}</textarea>
                                    @error('msg_details')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror

                                </div>
                            </div>
                        </div>


                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Save') }} <i
                                    class="typcn typcn-clipboard"></i></button>
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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>



    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>



    <script type="text/javascript">
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
