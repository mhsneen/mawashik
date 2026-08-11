@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Orders') }}
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
    <style>
        .userlist-table {
            overflow-x: auto !important;
            padding-bottom: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.03);
        }
        .userlist-table table {
            min-width: 1450px;
        }
        .userlist-table th, .userlist-table td {
            vertical-align: middle !important;
            padding: 12px 14px !important;
            font-size: 13px;
        }
        .userlist-table thead th {
            background-color: #f8fafc !important;
            color: #334155;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .userlist-table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Orders') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
            </div>
        </div>


        <div class="d-flex my-xl-auto right-content">

            <div class="pr-1 mb-3 mb-xl-0">
                <a aria-controls="collapseExample" aria-expanded="false" data-toggle="collapse" href="#collapseExample"
                    type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-magnify"></i></a>

            </div>

            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route($routeName . '.export_excel', $request_search) }}" type="button" class="btn btn-dark "
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
                        action ="{{ route($routeName) }}">

                        <div class="row row-sm mg-b-20">



                            <div class="col-lg-3">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Order_Number') }}</p>
                                <input class="form-control " id="search_order_number" name="search_order_number"
                                    type="text" value="{{ request('search_order_number') }}">
                                @error('search_order_number')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->


                            <div class="col-lg-3">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Client_Name') }}</p>
                                <input class="form-control " id="search_client_name" name="search_client_name"
                                    type="text" value="{{ request('search_client_name') }}">
                                @error('search_client_name')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->


                            <div class="col-lg-3">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Client_Mobile') }}</p>
                                <input class="form-control " id="search_client_mobile" name="search_client_mobile"
                                    type="text" value="{{ request('search_client_mobile') }}">
                                @error('search_client_mobile')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->
                        </div>
                        <br />
                        <div class="row row-sm mg-b-20">
                            <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Order_Status') }}</p>

                                <select class="form-control select2" data-placeholder="{{ __('dashboard.Choose') }}"
                                    id="search_status_id" name="search_status_id">
                                    <option value="">
                                        {{ __('dashboard.Choose') }}
                                    </option>
                                    @foreach ($orderstatus as $ordersta)
                                        <option value="{{ $ordersta->id }}"
                                            {{ request('search_status_id') == $ordersta->id ? 'selected' : '' }}>
                                            {{ $ordersta->name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('search_status_id')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>



                            <div class="col-lg-3">
                                <p class="mg-b-10"> {{ __('dashboard.Date_From') }}</p>
                                <input class="form-control fc-datepicker" id="search_date_from" name="search_date_from"
                                    type="text" value="{{ request('search_date_from') }}">
                                @error('search_date_from')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>


                            <div class="col-lg-3">
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
                            <a href="{{ route($routeName) }}" class="btn btn-danger  "
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
                            <h4 class="card-title mg-b-0">{{ __('dashboard.Orders') }}</h4>
                            @can('orders.delete')
                                <div>
                                    <button class="btn btn-danger-gradient btn-block delete_all"
                                        data-url="{{ route($routeName . '.deleteAll') }}">{{ __('dashboard.Delete_All') }}</button>


                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border-top userlist-table">
                            <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>{{ __('dashboard.Order_number') }}</th>
                                        <th>{{ __('dashboard.Client') }}</th>
                                        <th>{{ __('dashboard.Order_Status') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.number_products') }}</th>
                                        <th>{{ __('dashboard.Total price before discount') }}</th>
                                        <th>{{ __('dashboard.Discount value') }}</th>
                                        <th>{{ __('dashboard.Tax amount') }}</th>
<th>{{ __('dashboard.Added_Tax_Value') }}</th>
<th>{{ __('dashboard.Payment_Status') }}</th>
<th>{{ __('dashboard.Delivery_Type') }}</th>
<th>{{ __('dashboard.Delivery_Price') }}</th>


<th>{{ __('dashboard.Driver') }}</th>
<th>{{ __('dashboard.Payment_Method') }}</th>
                                        <th>{{ __('dashboard.TOTAL_DUE') }}</th>

                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr id="tr_{{ $item->id }}">
                                            <th><input type="checkbox" class="sub_chk" data-id="{{ $item->id }}">
                                            </th>
                                            <td>{{ $item->order_number }}
                                                @if ($item->read == '0')
                                                    <span class="badge bg-info me-1">{{ __('dashboard.New') }}</span>
                                                @endif

                                            </td>
                                            <td>{{ $item->user->name }}</td>


<td>
    @php
        // لون مميز لكل حالة
        $statusColors = [
            1 => '#036FE7', // طلب جديد - أزرق
            2 => '#F5B700', // قيد التجهيز - أصفر/ذهبي
            3 => '#00797D', // تم التجهيز - تركواز
            4 => '#7B61FF', // قيد التوصيل - بنفسجي
            5 => '#28A745', // تم التسليم - أخضر
            6 => '#DC3545', // ملغي - أحمر
            7 => '#FF7A00', // بإنتظار الدفع - برتقالي
        ];

        // لون افتراضي في حال الحالة غير موجودة
        $statusColor = $statusColors[$item->status_id] ?? '#6C757D';
    @endphp

    <span
        class="label d-flex align-items-center"
        style="color: {{ $statusColor }};"
    >
        <div
            class="dot-label ml-1"
            style="bottom:1px;background-color: {{ $statusColor }};"
        ></div>

        {{ $item->status?->name ?? __('Unknown Status') }}
    </span>
</td>





                                            <td>{{ $item->created_at->diffForHumans() }}</td>

                                            <td>{{ $item->total_items }}</td>
                                            <td>{{ $item->total_price }}</td>
                                            <td>{{ $item->discount_amount }}
                                                @if ($item->coupon)
                                                    <br />
                                                    ({{ $item->coupon->discount_percentage }} %)
                                                    {{ $item->coupon->code }}
                                                @endif


                                            </td>
                                            <td>{{ $item->added_tax }} %</td>
                                            <td>{{ number_format($item->tax_amount, 2) }}</td>

<td>
    @php
        $paymentBadge = match($item->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed', 'cancelled' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    @endphp
    <span class="badge bg-{{ $paymentBadge }}">{{ $item->payment_status_label }}</span>
</td>

<td>
    @if ($item->delivery_type === 'pickup')
        <span class="badge" style="background-color:#E7F5FF;color:#0d6efd;padding:6px 12px;font-weight:600;border-radius:20px;">
            <i class="fa fa-store mr-1"></i> {{ $item->delivery_type_label }}
        </span>
    @else
        <span class="badge" style="background-color:#E9FBF0;color:#198754;padding:6px 12px;font-weight:600;border-radius:20px;">
            <i class="fa fa-truck mr-1"></i> {{ $item->delivery_type_label }}
        </span>
    @endif
</td>
<td>
    @if ($item->delivery_type === 'delivery')
        {{ number_format($item->delivery_price, 2) }}
    @else
        —
    @endif
</td>

<td>
    @if ($item->delivery_type === 'delivery')
        @if ($item->driver)
            <div class="d-flex align-items-center">
                <span class="avatar avatar-sm brround mr-2" style="background-color:#E9FBF0;color:#198754;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-id-badge"></i>
                </span>
                <div>
                    <div class="font-weight-bold">{{ $item->driver->name }}</div>
                    <small class="text-muted">{{ $item->driver->mobile }}</small>
                </div>
            </div>
        @else
            <span class="badge" style="background-color:#FFF4E5;color:#FF7A00;padding:6px 12px;font-weight:600;border-radius:20px;">
                <i class="fa fa-exclamation-circle mr-1"></i> {{ __('dashboard.No_Driver_Assigned') }}
            </span>
        @endif
    @else
        <span class="text-muted">—</span>
    @endif
</td>

<td>{{ $item->payment_method_label }}</td>

                                            <td style="font-weight: bold;line-height:25px;"> {{ $item->final_price }}

                                            </td>

                                            <td>
                                                <div class="d-flex my-xl-auto right-content">


                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.delete', $item->id) }}"
                                                            class="delete-confirm btn btn-danger btn-icon ml-2"
                                                            data-placement="top" data-toggle="tooltip"
                                                            title="{{ __('dashboard.Delete') }} "><i
                                                                class="las la-trash"></i></a>
                                                    </div>




                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.show', $item->id) }}"
                                                            class=" btn btn-success btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip" title="{{ __('dashboard.View') }} "><i
                                                                class="las la-eye"></i></a>
                                                    </div>

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

        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
        $('.delete-confirm').click(function() {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                    title: "{{ __('dashboard.Are_you_sure') }}",
                    text: "{{ __('dashboard.Do_you_really_delete') }}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "{{ __('dashboard.Yes_delete_it') }}",
                    cancelButtonText: "{{ __('dashboard.No_cancel') }}",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = url;
                    }
                });
        });





        $(document).ready(function() {




            $('#master').on('click', function(e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });


            $('.delete_all').on('click', function(e) {


                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });


                if (allVals.length <= 0) {
                    swal({
                        title: "{{ __('dashboard.No_Record_Delete') }}",
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "{{ __('dashboard.Close') }}",
                        closeOnCancel: true
                    });
                } else {


                    swal({
                            title: "{{ __('dashboard.Are_you_sure') }}",
                            text: "{{ __('dashboard.Do_you_really_delete') }}",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "{{ __('dashboard.Yes_delete_it') }}",
                            cancelButtonText: "{{ __('dashboard.No_cancel') }}",
                            closeOnConfirm: false,
                            closeOnCancel: true,
                            showLoaderOnConfirm: true

                        },
                        function(isConfirm) {
                            if (isConfirm) {

                                var join_selected_values = allVals.join(",");

                                $.ajax({
                                    url: "{{ route($routeName . '.deleteAll') }}",
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },

                                    data: 'ids=' + join_selected_values,
                                    success: function(data) {
                                        if (data['success']) {
                                            $(".sub_chk:checked").each(function() {
                                                $(this).parents("tr").remove();
                                            });


                                            swal({
                                                title: data['success'],
                                                type: "success",
                                                showCancelButton: true,
                                                showConfirmButton: false,
                                                cancelButtonText: "{{ __('dashboard.Close') }}",
                                                closeOnCancel: true
                                            });


                                        } else if (data['error']) {

                                            swal({
                                                title: data['error'],
                                                type: "error",
                                                showCancelButton: true,
                                                showConfirmButton: false,
                                                cancelButtonText: "{{ __('dashboard.Close') }}",
                                                closeOnCancel: true
                                            });

                                        } else {
                                            swal({
                                                title: "{{ __('dashboard.Error_DB') }}",
                                                type: "error",
                                                showCancelButton: true,
                                                showConfirmButton: false,
                                                cancelButtonText: "{{ __('dashboard.Close') }}",
                                                closeOnCancel: true
                                            });

                                        }
                                    },
                                    error: function(data) {

                                        swal({
                                            title: data.responseText,
                                            type: "error",
                                            showCancelButton: true,
                                            showConfirmButton: false,
                                            cancelButtonText: "{{ __('dashboard.Close') }}",
                                            closeOnCancel: true
                                        });
                                    }
                                });


                                $.each(allVals, function(index, value) {
                                    $('table tr').filter("[data-row-id='" + value + "']")
                                        .remove();

                                });

                            }

                        });

                }
            });





        });
    </script>
@endsection
