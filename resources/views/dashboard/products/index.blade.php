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

            <div class="card collapse @if ($errors->any()) show @endif" id="collapseExample">
                <div class="card-body ">
                    <form class="form-horizontal" method="GET" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName) }}">


                        <div class="row row-sm mg-b-20">
                            <div class="col-lg-4">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Name') }}</p>
                                <input class="form-control " id="search_name" name="search_name" type="text"
                                    value="{{ request('search_name') }}">
                                @error('search_name')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }}
                                    {{ __('dashboard.Product_Add_Category') }}</p>

                                <select class="form-control select2" data-placeholder="{{ __('dashboard.Choose') }}"
                                    id="search_category_id" name="search_category_id">
                                    <option value="0">
                                        {{ __('dashboard.Choose') }}
                                    </option>
                                    @foreach ($categories as $category)
                                        @if ($category->subCategories->count() > 0)
                                            <optgroup label="{{ $category->name }}">
                                                @foreach ($category->subCategories as $subcategory)
                                                    <option value="{{ $subcategory->id }}"
                                                        {{ request('search_category_id') == $subcategory->id ? 'selected' : '' }}>
                                                        @if (app()->getLocale() == 'en')
                                                            &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;
                                                            @endif {{ $subcategory->name }} @if (app()->getLocale() == 'ar')
                                                                &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;
                                                            @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $category->id }}"
                                                {{ request('search_category_id') == $category->id ? 'selected' : '' }}>
                                                @if (app()->getLocale() == 'en')
                                                    &raquo&nbsp;
                                                    @endif {{ $category->name }} @if (app()->getLocale() == 'ar')
                                                        &laquo&nbsp;
                                                    @endif
                                            </option>
                                        @endif
                                    @endforeach

                                </select>
                                @error('search_category_id')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror

                            </div><!-- col-4 -->
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Brands') }}</p>

                                <select class="form-control select2" id="search_brand_id" name="search_brand_id">
                                    <option value="0">
                                        {{ __('dashboard.Choose') }}
                                    </option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ request('search_brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('search_brand_id')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div><!-- col-4 -->
                        </div>

                        <hr />


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
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Price') }}</p>

                                <div class="row mg-t-10">
                                    <div class="col-lg-6">
                                        <select class="form-control select2" id="search_diffrnetPrice"
                                            name="search_diffrnetPrice">
                                            <option value=" ">
                                                {{ __('dashboard.Standard') }}
                                            </option>

                                            <option value="="
                                                {{ request('search_diffrnetPrice') == '=' ? 'selected' : '' }}>
                                                {{ __('dashboard.Equal') }}
                                            </option>

                                            <option value=">="
                                                {{ request('search_diffrnetPrice') == '>=' ? 'selected' : '' }}>
                                                {{ __('dashboard.greaterorequalto') }}
                                            </option>

                                            <option value="<="
                                                {{ request('search_diffrnetPrice') == '<=' ? 'selected' : '' }}>
                                                {{ __('dashboard.lessthanorequalto') }}
                                            </option>
                                        </select>
                                        @error('search_diffrnetPrice')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 ">
                                        <input class="form-control" id="search_netPrice" name="search_netPrice"
                                            data-parsley-type="number" placeholder="{{ __('dashboard.Price') }}"
                                            type="text" value="{{ request('search_netPrice') }}">
                                        @error('search_netPrice')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>



                            </div><!-- col-4 -->
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Quantity') }}</p>

                                <div class="row mg-t-10">
                                    <div class="col-lg-6">
                                        <select class="form-control select2" id="search_diffrquantity"
                                            name="search_diffrquantity">
                                            <option value=" ">
                                                {{ __('dashboard.Standard') }}
                                            </option>

                                            <option value="="
                                                {{ request('search_diffrquantity') == '=' ? 'selected' : '' }}>
                                                {{ __('dashboard.Equal') }}
                                            </option>

                                            <option value=">="
                                                {{ request('search_diffrquantity') == '>=' ? 'selected' : '' }}>
                                                {{ __('dashboard.greaterorequalto') }}
                                            </option>

                                            <option value="<="
                                                {{ request('search_diffrquantity') == '<=' ? 'selected' : '' }}>
                                                {{ __('dashboard.lessthanorequalto') }}
                                            </option>
                                        </select>
                                        @error('search_diffrquantity')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 ">
                                        <input class="form-control " id="search_quantity" name="search_quantity"
                                            data-parsley-type="number" placeholder="{{ __('dashboard.Quantity') }}"
                                            type="text" value="{{ request('search_quantity') }}">
                                        @error('search_quantity')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
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
                            <h4 class="card-title mg-b-0"> {{ __($namesection) }}




                            </h4>
                            <div>
                                <button class="btn btn-danger-gradient btn-block delete_all"
                                    data-url="{{ route($routeName . '.deleteAll') }}">{{ __('dashboard.Delete_All') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border-top ">
                            <table class="table card-table table-striped table-vcenter  mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>{{ __('dashboard.Name') }}</th>
                                        <th>{{ __('dashboard.Product_Add_Category') }}</th>
                                        <th>{{ __('dashboard.price') }}</th>
                                        <th>{{ __('dashboard.Product_Code') }}</th>
                                        <th>{{ __('dashboard.Quantity') }}</th>
                                        <th>{{ __('dashboard.Efficiency_Status') }}</th>
                                        <th>{{ __('dashboard.Main_Image') }}</th>
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
                                            <td>{{ $item->category->name }} </td>


                                            <td>


                                                <div class="product-price">
                                                    <span class="text-muted"> {{ __('dashboard.price') }} :
                                                        {{ $item->Formatted_netPrice }}
                                                        {{ __('dashboard.SAR') }}</span>

                                                    @if ($item->has_sale)
                                                        <div class="sale-info mt-1">
                                                            <span
                                                                class="text-danger">{{ __('dashboard.Price after discount') }}
                                                                :
                                                                {{ $item->current_price }}
                                                                {{ __('dashboard.SAR') }}</span><br>
                                                            <span
                                                                class="badge bg-warning text-dark">{{ __('dashboard.Debit') }}
                                                                {{ $item->Formatted_sale_price }}%</span>
                                                        </div>
                                                    @endif
                                                </div>



                                            </td>
                                            <td>{{ $item->sku }} </td>
                                            <td>
                                                @if (is_null($item->quantity))
                                                    <span class="badge bg-info">{{ __('dashboard.Unlimited') }}</span>
                                                @else
                                                    {{ $item->quantity }} {{ $item->unit->name ?? '' }}
                                                @endif
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
                                            <td>
                                                @if ($item->image != '')
                                                    <div class="slider-image-wrapper">
                                                        <img src="{{ $item->image_url }}" alt="slider image">
                                                    </div>
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


                                                </div>




                                            </td>
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
