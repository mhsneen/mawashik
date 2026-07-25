@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Reports') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">

    <!--Internal  Datetimepicker-slider css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}"
        rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.css') }}"
        rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Reports') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Products_purchased') }}</span>
            </div>
        </div>


        <div class="d-flex my-xl-auto right-content">

            <div class="pr-1 mb-3 mb-xl-0">
                <a aria-controls="collapseExample" aria-expanded="false" data-toggle="collapse" href="#collapseExample"
                    type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-magnify"></i></a>

            </div>


        </div>

    </div>


    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">



        <div class="col-xl-12">



            <div class="card collapse @if ($errors->any()) show @endif" id="collapseExample">
                <div class="card-body ">
                    <form class="form-horizontal" method="GET" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route('reports.product_purchased') }}">



                        <div class="row row-sm mg-b-20">


                            <div class="col-lg-6">
                                <p class="mg-b-10"> {{ __('dashboard.Date_From') }}</p>
                                <input class="form-control fc-datepicker" id="search_date_from" name="search_date_from"
                                    type="text" value="{{ request('search_date_from') }}">
                                @error('search_date_from')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>


                            <div class="col-lg-6">
                                <p class="mg-b-10"> {{ __('dashboard.Date_To') }}</p>
                                <input class="form-control fc-datepicker" id="search_date_to" name="search_date_to"
                                    type="text" value="{{ request('search_date_to') }}">
                                @error('search_date_to')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>

                        </div><!-- col-4 -->







                        <div class="card-footer text-left">
                            <button class="btn btn-dark " type="submit">{{ __('dashboard.Search') }} <i
                                    class="mdi mdi-magnify"></i></button>
                            <a href="{{ route('reports.product_purchased') }}" class="btn btn-danger  "
                                type="submit">{{ __('dashboard.Clear_Search') }} <i class="mdi mdi-cancel"></i></a>
                        </div>
                    </form>
                </div>
            </div>


            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card mg-b-20">
                @if (count($items) > 0)
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0">{{ __('dashboard.Products_purchased') }}</h4>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border-top userlist-table">
                            <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.Product') }}</th>
                                        <th>{{ __('dashboard.Quantity') }}</th>
                                        <th>{{ __('dashboard.Order_Number') }}</th>
                                        <th>{{ __('dashboard.Price') }}</th>
                                        <th>{{ __('dashboard.The_date_and_time_of_purchase') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="card-aside-img"
                                                        style="margin-left:20px; margin-right:20px; ">
                                                        <img src="@if (empty($item->product->image)) {{ URL::asset('assets/dashboard/img/faces/6.jpg') }}@else{{ URL::asset('uploads/products/thumbnail/' . $item->product->image . '') }} @endif"
                                                            alt="img" class="h-60 w-60">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="card-item-desc mt-0">
                                                            <h6 class="font-weight-semibold mt-0 text-uppercase">
                                                                {{ $item->product->name }}</h6>
                                                            <dl class="card-item-desc-1">
                                                                <dt>{{ __('dashboard.Product_Add_Category') }}: </dt>
                                                                <dd>{{ $item->product->category->name }}</dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $item->quantity }}
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    {{ $item->order_id }}
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    {{ floatval($item->unit_price) }} SAR
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $item->created_at->format('d/m/Y') }}<br />{{ $item->created_at->format('h:i:s A') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                        <br />
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>



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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!--Internal  Sweet-Alert js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>

    <!--Internal  Datepicker js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>




    <script type="text/javascript">
        // Datepicker
        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'yy-mm-dd',
        });
    </script>
@endsection
