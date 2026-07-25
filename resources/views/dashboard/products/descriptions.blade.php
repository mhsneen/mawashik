@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Products') }} - {{ __('dashboard.Edit') }} - {{ __('dashboard.Product_Description') }}
@endsection
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / {{ __('dashboard.Edit') }} / {{ __('dashboard.Product_Description') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('products') }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    @include('dashboard.products.steps', ['active' => 2, 'id' => $item->id])
    <!-- row -->
    <div class="row row-sm">




        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')


            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route('products.updatedescription') }}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{ $item->id }}" />



                        <div class="col-md-12">
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label pd-t-10">{{ __('dashboard.Short_Description') }} ( AR ) :
                                        </label>

                                    </div>
                                    <div class="col-md-8">
                                        <textarea class="form-control tx_R" placeholder="{{ __('dashboard.Short_Description') }} (AR)" rows="5"
                                            id="description_ar" name="description_ar"> @if (!empty($item->translate('ar')->description))
{{ $item->translate('ar')->description }}
@endif
</textarea>
                                        @error('description_ar')
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
                                        <label class="form-label pd-t-10">{{ __('dashboard.Short_Description') }} ( EN ) :
                                        </label>

                                    </div>
                                    <div class="col-md-8">
                                        <textarea class="form-control tx_L" placeholder="{{ __('dashboard.Short_Description') }} (EN)" rows="5"
                                            id="description_en" name="description_en"> @if (!empty($item->translate('en')->description))
{{ $item->translate('en')->description }}
@endif
</textarea>
                                        @error('description_en')
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
                                        <label class="form-label pd-t-10">{{ __('dashboard.Full_Description') }} ( AR ) :
                                        </label>

                                    </div>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="details_ar" name="details_ar"> @if (!empty($item->translate('ar')->details))
{{ $item->translate('ar')->details }}
@endif
</textarea>
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
                                        <label class="form-label pd-t-10">{{ __('dashboard.Full_Description') }} ( EN ) :
                                        </label>

                                    </div>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="details_en" name="details_en"> @if (!empty($item->translate('en')->details))
{{ $item->translate('en')->details }}
@endif
</textarea>

                                        @error('details_en')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
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
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>



    <script>
        CKEDITOR.replace('details_ar', {
            language: '{{ app()->getLocale() }}',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });


        CKEDITOR.replace('details_en', {
            language: 'en',
            filebrowserUploadMethod: 'form',
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files',

        });
    </script>


    @include('ckfinder::setup')
@endsection
