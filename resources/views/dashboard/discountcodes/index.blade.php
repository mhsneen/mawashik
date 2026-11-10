@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> {{ __($namesection) }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
            </div>
        </div>

        <div class="d-flex my-xl-auto right-content">

            <div class="pr-1 mb-3 mb-xl-0">
                <a aria-controls="collapseExample" aria-expanded="false" data-toggle="collapse" href="#collapseExample"
                    type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-magnify"></i></a>
            </div>

            <div class="mb-3 mb-xl-0">
                <div class="btn-group dropdown">
                    <a href="{{ route($routeName . '.create') }}" class="btn btn-primary"> <i class="fa fa-plus"></i>
                        {{ __('dashboard.Add_New') }} </a>


                </div>
            </div>



        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">



        <div class="col-xl-12">

            <div class="card collapse " id="collapseExample">
                <div class="card-body ">
                    <form class="form-horizontal" method="GET" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName) }}">





                        <div class="row row-sm mg-b-20">




                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Status') }}</p>

                                <select class="form-control select2" id="search_status" name="search_status">
                                    <option value="0">
                                        {{ __('dashboard.Choose') }}
                                    </option>
                                    <option value="Active" {{ request('search_status') == 'Active' ? 'selected' : '' }}>
                                        {{ __('dashboard.Active') }}
                                    </option>

                                    <option value="Finished"
                                        {{ request('search_status') == 'Finished' ? 'selected' : '' }}>
                                        {{ __('dashboard.Finished') }}
                                    </option>

                                    <option value="Pending" {{ request('search_status') == 'Pending' ? 'selected' : '' }}>
                                        {{ __('dashboard.Pending') }}
                                    </option>

                                    <option value="Stopped" {{ request('search_status') == 'Stopped' ? 'selected' : '' }}>
                                        {{ __('dashboard.Stopped') }}
                                    </option>


                                </select>
                                @error('search_status')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->
                        </div>
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
                            <h4 class="card-title mg-b-0"> {{ __($namesection) }}</h4>
                            <div>
                                <button class="btn btn-danger-gradient btn-block delete_all"
                                    data-url="{{ route($routeName . '.deleteAll') }}">{{ __('dashboard.Delete_All') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border-top userlist-table">
                            <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>{{ __('dashboard.Discount Code') }}</th>
                                        <th>{{ __('dashboard.Discount Percentage') }}</th>
                                        <th>{{ __('dashboard.Start Date') }}</th>
                                        <th>{{ __('dashboard.End Date') }}</th>
                                        <th>{{ __('dashboard.How to use') }}</th>
                                        <th>{{ __('dashboard.Number of uses') }}</th>
                                        <th>{{ __('dashboard.Status') }}</th>

                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr id="tr_{{ $item->id }}">
                                            <th><input type="checkbox" class="sub_chk" data-id="{{ $item->id }}">
                                            </th>
                                            <td> {{ $item->code }} </td>
                                            <td> {{ $item->discount_percentage }} %</td>
                                            <td> {{ $item->start_date->format('Y-m-d') }} </td>
                                            <td> {{ $item->end_date->format('Y-m-d') }} </td>



                                            <td>
                                                @if ($item->usage_type == 'once')
                                                    {{ __('dashboard.Single-use') }}
                                                @endif
                                                @if ($item->usage_type == 'multiple')
                                                    {{ __('dashboard.Multi-use') }}
                                                @endif
                                                @if ($item->usage_type == 'limited')
                                                    {{ __('dashboard.Specific number') }} ({{ $item->max_usage }})
                                                @endif

                                            </td>
                                            <td style=" color:green;font-size:12pt; font-weight:bold">
                                                {{ $item->usage_count }} </td>


                                            <td>{!! $item->status !!}</td>

                                            <td>
                                                <div class="d-flex my-xl-auto right-content">
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.edit', $item->id) }}"
                                                            class="btn btn-info btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip" title="{{ __('dashboard.Edit') }} "> <i
                                                                class="las la-pen"></i></a>
                                                    </div>
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.delete', $item->id) }}"
                                                            class="delete-confirm btn btn-danger btn-icon ml-2"
                                                            data-placement="top" data-toggle="tooltip"
                                                            title="{{ __('dashboard.Delete') }} "><i
                                                                class="las la-trash"></i></a>
                                                    </div>
                                                    <a href="{{ route($routeName . '.user_usage', $item->id) }}"
                                                       class=" btn btn-success  btn-icon ml-2"
                                                        data-placement="top" data-toggle="tooltip"
                                                        title="{{ __('dashboard.View') }} "><i
                                                            class="las la-user"></i></a>

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
    <!--Internal  Sweet-Alert js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script type="text/javascript">
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

                                    $('table tr.' + value + '_sun').remove();
                                });

                            }

                        });

                }
            });





        });
    </script>
@endsection
