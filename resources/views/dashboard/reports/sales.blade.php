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
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.The_Sales') }}</span>
            </div>
        </div>


        <div class="d-flex my-xl-auto right-content">

            <div class="pr-1 mb-3 mb-xl-0">
                <a aria-controls="collapseExample" aria-expanded="false" data-toggle="collapse" href="#collapseExample"
                    type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-magnify"></i></a>

            </div>

            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('reports.export_sales_excel', $request_search) }}" type="button" class="btn btn-dark "
                    data-placement="top" data-toggle="tooltip" style="padding: 8px 20px;"
                    title="{{ __('dashboard.excel_export') }} ">{{ __('dashboard.excel_export') }} <i
                        class="lar la-file-excel" style="font-size: 14pt"></i></a>
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
                        action ="{{ route('reports.sales') }}">



                        <div class="row row-sm mg-b-20">


                            <div class="col-lg-4">
                                <p class="mg-b-10"> {{ __('dashboard.Date_From') }}</p>
                                <input class="form-control fc-datepicker" id="search_date_from" name="search_date_from"
                                    type="text" value="{{ request('search_date_from') }}">
                                @error('search_date_from')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>


                            <div class="col-lg-4">
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
                            <a href="{{ route('reports.sales') }}" class="btn btn-danger  "
                                type="submit">{{ __('dashboard.Clear_Search') }} <i class="mdi mdi-cancel"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h5>{{ __('dashboard.number_of_orders') }} </h5>
                        <b>{{ $summary['total_orders'] }}</b>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h5> {{ __('dashboard.TOTAL_SALES') }}</h5>
                        <b>{{ $summary['total_sales'] }}</b>
                    </div>
                </div>


            </div>
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card mg-b-20">
                @if (count($orders) > 0)
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0">{{ __('dashboard.The_Sales') }}</h4>

                        </div>
                    </div>



                    <div class="card-body">




                        <div class="table-responsive border-top userlist-table">
                            <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                                <thead>
                                    <tr>


                                        <th>#</th>
                                        <th>{{ __('dashboard.Order_Number') }}</th>
                                        <th>{{ __('dashboard.Client') }}</th>
                                        <th>{{ __('dashboard.TOTAL_DUE') }}</th>
                                        <th>{{ __('dashboard.Status') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>

                                    </tr>
                                </thead>
                                @foreach ($orders as $order)
                                    <tbody>


                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->user->name ?? '-' }}</td>
                                        <td>{{ $order->final_price }} {{ $order->currency }}</td>
                                        <td>{{ $order->status->name ?? '-' }}</td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>

                                    </tbody>
                                @endforeach
                            </table>

                        </div>
                        <br />
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>

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


        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>
@endsection
