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
                <h4 class="content-title mb-0 my-auto">{{ __($namesection) }}</h4><span
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


            <div class="card collapse " id="collapseExample">
                <div class="card-body ">
                    <form class="form-horizontal" method="GET" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName) }}">

                        <div class="row row-sm mg-b-20">
                            <div class="col-lg-4">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Name') }}
                                </p>
                                <input class="form-control " id="search_name" name="search_name" type="text"
                                    value="{{ request('search_name') }}">
                                @error('search_name')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->


                            <div class="col-lg-4">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Mobile') }}</p>
                                <input class="form-control " id="search_mobile" name="search_mobile" type="text"
                                    value="{{ request('search_mobile') }}">
                                @error('search_mobile')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->



                        </div>




                        <div class="row row-sm mg-b-20">



                            <div class="col-lg-4">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Efficiency_Status') }}
                                </p>
                                <div class="row mg-t-10">
                                    <div class="col-lg-4">
                                        <label class="rdiobox"><input name="search_active" id="search_active" type="radio"
                                                value="1" @if (request('search_active') == '1') checked @endif>
                                            <span>{{ __('dashboard.effective') }}</span></label>
                                    </div>
                                    <div class="col-lg-4 ">
                                        <label class="rdiobox"><input name="search_active" id="search_active" type="radio"
                                                value="2" @if (request('search_active') == '2') checked @endif>
                                            <span>{{ __('dashboard.Inactive') }}</span></label>
                                    </div>
                                </div>
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
                            <h4 class="card-title mg-b-0">{{ __($namesection) }}</h4>
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
                                        <th>{{ __('dashboard.Name') }}</th>
                                        <th>{{ __('dashboard.Mobile') }}</th>
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
                                            <td>
                                                {{ $item->mobile }}
                                            </td>


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
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#ModalChangeActive{{ $item->id }}"
                                                            class=" btn btn-info  btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip"
                                                            title="{{ __('dashboard.Activate_Deactivate') }} "><i
                                                                class="las la-user"></i></a>
                                                    </div>
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#ModalSendingMessage{{ $item->id }}"
                                                            class=" btn btn-warning  btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip"
                                                            title="{{ __('dashboard.Sending_Message') }} "><i
                                                                class="las la-sms"></i></a>
                                                    </div>

                                                    {{-- 
                                                   
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#ModalChangePassword{{ $item->id }}"
                                                            class=" btn btn-dark btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip"
                                                            title="{{ __('dashboard.Edit_password') }} "><i
                                                                class="las la-key"></i></a>
                                                    </div>
                                                   --}}


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
                                            @include('dashboard.clients.modal.changeactive')
                                            @include('dashboard.clients.modal.sendingmessage')
                                            @include('dashboard.clients.modal.changepassword')

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
                    // check if modal_div element exists on the page
                    if ($('#ModalChangeActive{{ $item->id }}').length > 0) {

                        $('#modal_ChangeActive_submit_button{{ $item->id }}').click(function(event) {

                            $('#FormModalChangeActive{{ $item->id }} select').each(function() {
                                if ($(this).parsley().validate() !== true)
                                    event.preventDefault();
                            });

                        });
                    }
                });

                $(function() {

                    //check if modal_div element exists on the page
                    if ($('#ModalChangePassword{{ $item->id }}').length > 0) {

                        //Attach Parsley validation to the modal input elements


                        //On modal submit button click, validate all the input fields
                        $('#modal_ChangePassword_submit_button{{ $item->id }}').click(function(event) {


                            $('#ModalChangePassword{{ $item->id }} input').each(function() {
                                if ($(this).parsley().validate() !== true)
                                    event.preventDefault();
                            })


                        });
                    }
                });


                $(function() {

                    //check if modal_div element exists on the page
                    if ($('#ModalSendingMessage{{ $item->id }}').length > 0) {

                        //Attach Parsley validation to the modal input elements


                        //On modal submit button click, validate all the input fields
                        $('#modal_sendingmsg_submit_button{{ $item->id }}').click(function(event) {


                            $('#ModalSendingMessage{{ $item->id }} select,#ModalSendingMessage{{ $item->id }} textarea,#ModalSendingMessage{{ $item->id }} input')
                                .each(function() {
                                    if ($(this).parsley().validate() !== true)
                                        event.preventDefault();
                                })


                        });
                    }
                });

                @php
                    $nameerror = 'changepassbag_' . $item->id;
                    $nameerrorsendingmsg = 'sendingmessagebag_' . $item->id;
                    $nameerroractiveuser = 'changeactivebag_' . $item->id;
                @endphp
                @if ($errors->$nameerror->any())
                    $('#ModalChangePassword{{ $item->id }}').modal({
                        show: true
                    });
                @endif

                @if ($errors->$nameerrorsendingmsg->any())
                    $('#ModalSendingMessage{{ $item->id }}').modal({
                        show: true
                    });
                @endif


                @if ($errors->$nameerroractiveuser->any())
                    $('#ModalChangeActive{{ $item->id }}').modal({
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
