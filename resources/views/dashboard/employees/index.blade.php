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



            @can('employees.create')
                <div class="mb-3 mb-xl-0">
                    <div class="btn-group dropdown">
                        <a href="{{ route($routeName . '.create') }}" class="btn btn-primary"> <i class="fa fa-plus"></i>
                            {{ __('dashboard.Add_New') }} </a>


                    </div>
                </div>
            @endcan


        </div>
    </div>


    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">



        <div class="col-xl-12">




            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card mg-b-20">
                @if (count($items) > 0)
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0">{{ __('dashboard.Clients') }}</h4>
                            @can('employees.delete')
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
                                        <th>{{ __('dashboard.Name') }}</th>
                                        <th>{{ __('dashboard.Mobile') }}</th>
                                        <th>{{ __('dashboard.Email') }}</th>
                                        <th>{{ __('dashboard.Efficiency_Status') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr id="tr_{{ $item->id }}">
                                            <th><input type="checkbox" class="sub_chk" data-id="{{ $item->id }}">
                                            </th>
                                            <td>{{ $item->name }} </td>

                                            <td style="direction: ltr">{{ $item->mobile }} </td>
                                            <td>{{ $item->email }} </td>
                                            <td>
                                                @if ($item->is_active == 1)
                                                    <span class="label text-success d-flex">
                                                        <div class="dot-label bg-success ml-1"></div>
                                                        {{ __('dashboard.effective') }}
                                                    </span>
                                                @endif
                                                @if ($item->is_active == 0)
                                                    <span class="label text-muted d-flex">
                                                        <div class="dot-label bg-gray-300 ml-1"></div>
                                                        {{ __('dashboard.Inactive') }}
                                                    </span>
                                                @endif

                                            </td>

                                            <td>{{ $item->created_at->diffForHumans() }}</td>
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
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#ModalChangePassword{{ $item->id }}"
                                                            class=" btn btn-dark btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip"
                                                            title="{{ __('dashboard.Edit_password') }} "><i
                                                                class="las la-key"></i></a>
                                                    </div>



                                                </div>




                                            </td>
                                            @include('dashboard.clients.modal.changepassword')
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                        <br />
                        {{ $items->links() }}
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
            @foreach ($items as $item)

                $(function() {

                    //check if modal_div element exists on the page
                    if ($('#ModalChangePassword{{ $item->id }}').length > 0) {

                        //Attach Parsley validation to the modal input elements


                        //On modal submit button click, validate all the input fields
                        $('#modal_div_submit_button{{ $item->id }}').click(function(event) {


                            $('#ModalChangePassword{{ $item->id }} input').each(function() {
                                if ($(this).parsley().validate() !== true)
                                    event.preventDefault();
                            })


                        });
                    }
                });




                @php
                    $nameerror = 'changepassbag_' . $item->id;
                    $nameerrorsendingmsg = 'sendingmessagebag_' . $item->id;
                @endphp
                @if ($errors->$nameerror->any())
                    $('#ModalChangePassword{{ $item->id }}').modal({
                        show: true
                    });
                @endif
            @endforeach







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
