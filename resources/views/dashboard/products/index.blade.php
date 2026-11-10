@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
    {{-- Lightbox2 CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <style>
        .description-text { display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; vertical-align: bottom; transition: max-width 0.3s; }
        .description-text.expanded { white-space: normal; max-width: 100%; }
        .read-more { display: none; cursor: pointer; font-size: 12px; color: #007bff; }
    </style>
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
                {{-- التاجر يضيف المنتجات من التطبيق — لا يوجد إضافة من الـ Dashboard --}}
                <a href="{{ route('products-approvals.pending') }}" class="btn btn-warning ml-2" data-toggle="tooltip" title="{{ __('dashboard.Products_Pending_Approval') }}">
                    <i class="fa fa-hourglass-half"></i> {{ __('dashboard.Pending_Approvals') }}
                </a>
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
                            </div>
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

                            </div>
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <p class="mg-b-10">{{ __('dashboard.search_by') }} {{ __('dashboard.Traders') }}</p>

                                <select class="form-control select2" id="search_trader_id" name="search_trader_id">
                                    <option value="0">
                                        {{ __('dashboard.Choose') }}
                                    </option>
                                    @foreach ($traders as $trader)
                                        <option value="{{ $trader->id }}"
                                            {{ request('search_trader_id') == $trader->id ? 'selected' : '' }}>
                                            {{ $trader->name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('search_trader_id')
                                    <span class="tx-danger">{{ $message }} </span>
                                @enderror
                            </div>
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
                            </div>
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

                            </div>
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
                            </div>
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
                        <div class="table-responsive border-top ">
                            <table class="table card-table table-striped table-vcenter  mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>{{ __('dashboard.Name') }}</th>
                                        <th>{{ __('dashboard.Trader_Name') }}</th>
                                        <th>{{ __('dashboard.Product_Add_Category') }}</th>
                                        <th>{{ __('dashboard.price') }}</th>
                                        <th>{{ __('dashboard.Quantity') }}</th>
                                        <th>{{ __('dashboard.Slaughter') }}</th>
                                        <th>{{ __('dashboard.Product_Status') }}</th>
                                        <th>{{ __('dashboard.Efficiency_Status') }}</th>
                                        <th>{{ __('dashboard.Main_Image') }}</th>
                                        <th>{{ __('dashboard.Created_at') }}</th>
                                        <th>{{ __('dashboard.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr id="tr_{{ $item->id }}">
                                            <th>{{ $loop->iteration }}</th>
                                            <td>
                                                {{ $item->name }}
                                                {{-- وصف المنتج مع زر المزيد --}}
                                                @if($item->translate(app()->getLocale())?->description)
                                                    <div class="description-container mt-1">
                                                        <span class="description-text">
                                                            {{ $item->translate(app()->getLocale())->description }}
                                                        </span>
                                                        <a href="javascript:void(0)" class="read-more">المزيد</a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->trader)
                                                    <a href="{{ route('traders.show', $item->trader->id) }}" class="text-info">
                                                        {{ $item->trader->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
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
                                            <td>
                                                @if (is_null($item->quantity))
                                                    <span class="badge bg-info">{{ __('dashboard.Unlimited') }}</span>
                                                @else
                                                    {{ $item->quantity }} {{ $item->unit->name ?? '' }}
                                                    <a href="#" data-toggle="modal" data-target="#stock-modal-{{ $item->id }}" class="btn btn-warning btn-sm ml-1" data-toggle="tooltip" title="{{ __('dashboard.Update_Stock') }}"><i class="fa fa-edit"></i></a>
                                                @endif
                                            </td>

                                            {{-- عمود خدمة الذبح --}}
                                            <td>
                                                @if ($item->slaughter_available)
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-check mr-1"></i>{{ __('dashboard.Available') }}
                                                    </span>
                                                    @if ($item->slaughter_price)
                                                        <small class="d-block text-muted">{{ number_format($item->slaughter_price, 2) }} {{ __('dashboard.SAR') }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- عمود حالة الاعتماد --}}
                                            <td>
                                                @php
                                                    $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'secondary'];
                                                    $statusLabel = 'dashboard.' . ucfirst($item->product_status ?? 'pending');
                                                @endphp
                                                <span class="badge badge-{{ $statusColors[$item->product_status] ?? 'secondary' }}">
                                                    {{ __($statusLabel) }}
                                                </span>
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
                                                {{-- الصور والفيديو --}}
                                                <div style="display: flex; gap: 3px; flex-wrap: wrap;">
                                                    @if ($item->image != '')
                                                        <a href="{{ $item->base_image_url }}" data-lightbox="product-{{ $item->id }}" data-title="{{ $item->name }}">
                                                            <div class="slider-image-wrapper" style="cursor: pointer;">
                                                                <img src="{{ $item->image_url }}" alt="slider image" loading="lazy">
                                                            </div>
                                                        </a>
                                                    @endif

                                                    {{-- باقي الصور من مجلد imagestore --}}
                                                    @if($item->productimages && $item->productimages->count() > 0)
                                                        @foreach($item->productimages as $img)
                                                            <a href="{{ asset('uploads/imagestore/' . $img->filename) }}" data-lightbox="product-{{ $item->id }}" data-title="{{ $item->name }}">
                                                                <img src="{{ asset('uploads/imagestore/' . $img->filename) }}" width="40" style="border-radius: 5px;" loading="lazy">
                                                            </a>
                                                        @endforeach
                                                    @endif

                                                    {{-- الفيديو عبر مودال --}}
                                                 @if($item->video_url)
    <a href="javascript:void(0)" onclick="playVideo('{{ asset('uploads/products/' . $item->video_url) }}')">
        <div style="width: 40px; height: 40px; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
            <i class="fa fa-play"></i>
        </div>
    </a>
@endif
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}</td>

                                            <td>
                                                <div class="d-flex my-xl-auto right-content">
                                                    {{--
                                                     <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.edit', $item->id) }}"
                                                            class="btn btn-info btn-icon ml-2" data-placement="top"
                                                            data-toggle="tooltip" title="{{ __('dashboard.Edit') }}"> <i
                                                                class="las la-pen"></i></a>
                                                    </div>

                                                    --}}


                                                    {{-- إيقاف/تفعيل المنتج --}}
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        @if ($item->is_active == 1)
                                                            <form action="{{ route('products.toggleActive', $item->id) }}" method="POST" style="display:inline">
                                                                @csrf
                                                                <input type="hidden" name="is_active" value="0">
                                                                <button type="submit" class="btn btn-warning btn-icon ml-2" data-placement="top" data-toggle="tooltip" title="{{ __('dashboard.Suspend_Product') }}" onclick="return confirm('{{ __('dashboard.Confirm_Suspend_Product') }}')">
                                                                    <i class="fa fa-pause"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('products.toggleActive', $item->id) }}" method="POST" style="display:inline">
                                                                @csrf
                                                                <input type="hidden" name="is_active" value="1">
                                                                <button type="submit" class="btn btn-success btn-icon ml-2" data-placement="top" data-toggle="tooltip" title="{{ __('dashboard.Activate_Product') }}">
                                                                    <i class="fa fa-play"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                    <div class="pr-1 mb-3 mb-xl-0">
                                                        <a href="{{ route($routeName . '.delete', $item->id) }}"
                                                            class="delete-confirm btn btn-danger btn-icon ml-2"
                                                            data-placement="top" data-toggle="tooltip"
                                                            title="{{ __('dashboard.Delete') }}"><i
                                                                class="las la-trash"></i></a>
                                                    </div>
                                                </div>

                                                {{-- مودال تحديث المخزون --}}
                                                <div class="modal fade" id="stock-modal-{{ $item->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form action="{{ route('products.updateStock', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ __('dashboard.Update_Stock') }}: {{ $item->name }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="ckbox">
                                                                            <input type="checkbox" name="track_quantity" value="1" {{ $item->track_quantity ? 'checked' : '' }}>
                                                                            <span>{{ __('dashboard.track_quantity') }}</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('dashboard.Quantity') }}</label>
                                                                        <input type="number" name="quantity" class="form-control" min="0" step="0.01" value="{{ $item->quantity }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                                                                    <button type="submit" class="btn btn-primary">{{ __('dashboard.Save') }}</button>
                                                                </div>
                                                            </div>
                                                        </form>
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
    </div>

    {{-- مودال عرض الفيديو --}}
    <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="close position-absolute" style="top: 10px; left: 10px; z-index: 1051;" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white; font-size: 24px;">&times;</span>
                    </button>
                    <video id="productVideo" width="100%" controls autoplay></video>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>

    <!--Internal  Sweet-Alert js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>

    {{-- Lightbox2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

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
                            }
                        });
                }
            });

            // إعدادات Lightbox
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': "صورة %1 من %2"
            });

            // تشغيل الفيديو
            function playVideo(url) {
                $('#productVideo').attr('src', url);
                $('#videoModal').modal('show');
            }
            $('#videoModal').on('hidden.bs.modal', function () {
                $('#productVideo').attr('src', '');
            });

            // وصف المنتج - إظهار وإخفاء
            $('.description-container').each(function() {
                var text = $(this).find('.description-text');
                if (text[0].scrollWidth > text.innerWidth()) {
                    $(this).find('.read-more').show();
                }
            });
            $(document).on('click', '.read-more', function() {
                var text = $(this).siblings('.description-text');
                if (text.hasClass('expanded')) {
                    text.removeClass('expanded');
                    $(this).text('المزيد');
                } else {
                    text.addClass('expanded');
                    $(this).text('إخفاء');
                }
            });
        });
    </script>
@endsection
