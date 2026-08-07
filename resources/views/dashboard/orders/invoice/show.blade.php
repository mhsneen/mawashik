@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Bill') }} # {{ $item->order_number }}
@endsection
@section('css')
<style>
    @media print {
       .breadcrumb-header {
        visibility: hidden;

       }
       .main-header{
        visibility: hidden;

       }
       .main-footer{
        visibility: hidden;
       }
       body {-webkit-print-color-adjust: exact;}
       footer {page-break-after: always;}
.main-content-body-invoice{
    margin-top:-200px !important;
}
    }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between ">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Bill') }} # {{ $item->order_number }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View') }}</span>
            </div>
        </div>

        <div class="d-flex my-xl-auto right-content">

            <div class="pr-1 mb-3 mb-xl-0">
                <button type="button" onclick="javascript:window.print();" class="btn btn-danger btn-icon ml-2" ><i class="mdi mdi-printer"></i></button>
            </div>
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route($routeName . '.invoice',[$item->id,1]) }}" class="btn btn-purple  btn-icon ml-2" data-placement="top" data-toggle="tooltip"
                title="{{ __('dashboard.Download') }} "><i class="mdi mdi-file-pdf"></i></a>
            </div>
 <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route($routeName . '.show',$item->id) }}" data-placement="top" data-toggle="tooltip"
                    title="{{ __('dashboard.back') }} " type="button" class="btn btn-info btn-icon ml-2"><i class="typcn typcn-arrow-back-outline"></i></a>
            </div>
        </div>


    </div>


    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->

    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
     @include('dashboard.orders.invoice.invoice_data')
        </div><!-- COL-END -->
    </div>

        <!--/div-->

        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->


@endsection
@section('js')






@endsection
