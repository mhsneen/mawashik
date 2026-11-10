@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.TextPages') }}
@endsection
@section('css')
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.TextPages') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">



            <div class="mb-3 mb-xl-0">
                <div class="btn-group dropdown">
                    <a href="{{ route('textpages.create') }}" class="btn btn-primary"> <i class="fa fa-plus"></i>
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
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')




            <div class="card mg-b-20">
                @if (count($textpages) > 0)
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0">{{ __('dashboard.TextPages') }}</h4>
                            <div >
                                <button class="btn btn-danger-gradient btn-block delete_all"
                                    data-url="{{ route('textpages.deleteAll') }}">{{ __('dashboard.Delete_All') }}</button>
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
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($textpages as $textpage)
                                        <tr id="tr_{{ $textpage->id }}">
                                            <th><input type="checkbox" class="sub_chk"
                                                    data-id="{{ $textpage->id }}">
                                            </th>
                                            <td>{{ $textpage->name }} </td>


                                            <td>{{ $textpage->created_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="d-flex my-xl-auto right-content">
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route('textpages.edit', $textpage->id) }}"
                                                            class="btn btn-info btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip" title="{{ __('dashboard.Edit') }} "> <i
                                                                class="las la-pen"></i></a>
                                                    </div>
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route('textpages.delete', $textpage->id) }}"
                                                            class="delete-confirm btn btn-danger btn-icon ml-2"
                                                            data-placement="top" data-toggle="tooltip"
                                                            title="{{ __('dashboard.Delete') }} "><i
                                                                class="las la-trash"></i></a>
                                                    </div>


                                                </div>




                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>







                        </div>
                        <br/>
                        {{ $textpages->links() }}
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

    <script type="text/javascript">
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
                                    url: "{{ route('textpages.deleteAll') }}",
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
