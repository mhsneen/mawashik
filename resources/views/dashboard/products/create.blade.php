@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Add_New') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!---Internal Fileupload css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet"
        type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />

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
                <h4 class="content-title mb-0 my-auto"> {{ __($namesection) }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Add_New') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i
                    class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    @include('dashboard.products.steps', ['active' => 1, 'id' => '0'])
    <!-- row -->
    <div class="row row-sm">



        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName . '.store') }}">
                        @csrf



                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Name') }} ( AR ) : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R" id="name_ar" name="name_ar"
                                                placeholder="{{ __('dashboard.Name') }} (AR) " required=""
                                                type="text" value="{{ old('name_ar') }}">
                                            @error('name_ar')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Name') }} ( EN ) : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_L " id="name_en" name="name_en"
                                                placeholder="{{ __('dashboard.Name') }} (EN) " required=""
                                                type="text" value="{{ old('name_en') }}">
                                            @error('name_en')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>





                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Product_Add_Category') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="parsley-select " id="slWrappercategory_id">
                                                <select class="form-control select2" required=""
                                                    data-parsley-class-handler="#slWrappercategory_id"
                                                    data-parsley-errors-container="#slErrorContainercategory_id"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="category_id"
                                                    name="category_id">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>
                                                    @foreach ($categories as $category)
                                                        @if ($category->subCategories->count() > 0)
                                                            <optgroup label="{{ $category->name }}">
                                                                @foreach ($category->subCategories as $subcategory)
                                                                    <option value="{{ $subcategory->id }}"
                                                                        {{ old('category_id') == $subcategory->id ? 'selected' : '' }}>
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
                                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                                @if (app()->getLocale() == 'en')
                                                                    &raquo&nbsp;
                                                                    @endif {{ $category->name }} @if (app()->getLocale() == 'ar')
                                                                        &laquo&nbsp;
                                                                    @endif
                                                            </option>
                                                        @endif
                                                    @endforeach

                                                </select>


                                                <div id="slErrorContainercategory_id">
                                                    @error('category_id')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Brands') }}: </label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="parsley-select " id="slWrapperbrand_id">
                                                <select class="form-control select2"
                                                    data-parsley-class-handler="#slWrapperbrand_id"
                                                    data-parsley-errors-container="#slErrorContainerbrand_id"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="brand_id"
                                                    name="brand_id">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}"
                                                            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach

                                                </select>


                                                <div id="slErrorContainerbrand_id">
                                                    @error('brand_id')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>











                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Product_Code') }}: </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="sku" name="sku"
                                                placeholder="{{ __('dashboard.Product_Code') }}  " type="text"
                                                value="{{ old('sku') }}">
                                            @error('sku')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        {{-- تتبع المخزون --}}
<div class="row row-xs formgroup-wrapper">
    <div class="col-md-6">
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label pd-t-10">{{ __('dashboard.Track_Quantity') }}:</label>
                </div>
                <div class="col-md-8">
                    <div class="row mg-t-10">
                        <div class="col-lg-4">
                            <label class="rdiobox">
                                <input name="track_quantity" id="track_quantity_yes" type="radio" value="1"
                                    {{ old('track_quantity', '1') == '1' ? 'checked' : '' }}>
                                <span>{{ __('dashboard.Yes') }}</span>
                            </label>
                        </div>
                        <div class="col-lg-4">
                            <label class="rdiobox">
                                <input name="track_quantity" id="track_quantity_no" type="radio" value="0"
                                    {{ old('track_quantity') == '0' ? 'checked' : '' }}>
                                <span>{{ __('dashboard.No') }}</span>
                            </label>
                        </div>
                    </div>
                    <small class="text-muted">{{ __('dashboard.Track_Quantity_Help') }}</small>
                    @error('track_quantity')
                        <span class="tx-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- الكمية (تظهر فقط لو تتبع المخزون = نعم) --}}
    <div class="col-md-6" id="quantity_section">
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label pd-t-10">{{ __('dashboard.Quantity') }}:</label>
                </div>
                <div class="col-md-8">
                    <input class="form-control" id="quantity" name="quantity"
                        placeholder="{{ __('dashboard.Quantity') }}"
                        type="text" value="{{ old('quantity') }}"
                        data-parsley-type="number" step="0.01" min="0">
                    @error('quantity')
                        <span class="tx-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

{{-- الوحدات (اختياري - موجودة أصلاً) --}}
<div class="row row-xs formgroup-wrapper">
    <div class="col-md-6">
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label pd-t-10">{{ __('dashboard.Units') }}:</label>
                </div>
                <div class="col-md-8">
                    <div class="parsley-select" id="slWrapperunit_id">
                        <select class="form-control select2" id="unit_id" name="unit_id"
                            data-parsley-class-handler="#slWrapperunit_id"
                            data-parsley-errors-container="#slErrorContainerunit_id"
                            data-placeholder="{{ __('dashboard.Choose') }}">
                            <option value="">{{ __('dashboard.Choose') }}</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="slErrorContainerunit_id">
                            @error('unit_id')<span class="tx-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Efficiency_Status') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="row mg-t-10">
                                                <div class="col-lg-4">
                                                    <label class="rdiobox"><input name="is_active" id="is_active"
                                                            type="radio" value="1" required=""
                                                            @if (old('is_active') == '1') checked @endif>
                                                        <span>{{ __('dashboard.effective') }}</span></label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <label class="rdiobox"><input name="is_active" id="is_active"
                                                            type="radio" value="0" required=""
                                                            @if (old('is_active') == '0') checked @endif>
                                                        <span>{{ __('dashboard.Inactive') }}</span></label>
                                                </div>
                                            </div>
                                            @error('is_active')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.new_arrivals') }}:
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="row mg-t-10">
                                                <div class="col-lg-4">
                                                    <label class="rdiobox"><input name="new_arrivals" id="new_arrivals"
                                                            type="radio" value="1" required=""
                                                            @if (old('new_arrivals') == '1') checked @endif>
                                                        <span>{{ __('dashboard.Yes') }}</span></label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <label class="rdiobox"><input name="new_arrivals" id="new_arrivals"
                                                            type="radio" value="0" required=""
                                                            @if (old('new_arrivals') == '0') checked @endif>
                                                        <span>{{ __('dashboard.No') }}</span></label>
                                                </div>
                                            </div>
                                            @error('new_arrivals')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Calories') }}: </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="calories" name="calories"
                                                placeholder="{{ __('dashboard.Calories') }}  " type="text"
                                                value="{{ old('calories') }}">
                                            @error('calories')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>









                        <hr />
                        <br />
                        <div class="main-content-label mg-b-5"> {{ __('dashboard.price') }} </div>

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Base_price') }}: <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="netPrice" name="netPrice"
                                                placeholder="{{ __('dashboard.Base_price') }}  " required=""
                                                type="text" value="{{ old('netPrice') }}" data-parsley-type="number">
                                            @error('netPrice')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr />
                        <br />
                        <div class="main-content-label mg-b-5"> {{ __('dashboard.Price_in_Offer') }} </div>
                        <br />
                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Offer_start_date') }}:
                                            </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control fc-datepicker" id="salePrice_start"
                                                name="salePrice_start" type="text"
                                                data-parsley-mindate="{{ date('Y-m-d') }}"
                                                value="{{ old('salePrice_start') }}"
                                                placeholder="{{ __('dashboard.Offer_start_date') }}">
                                            @error('salePrice_start')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>






                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Offer_end_date') }}:
                                            </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control fc-datepicker" id="salePrice_end"
                                                name="salePrice_end" type="text" value="{{ old('salePrice_end') }}"
                                                placeholder="{{ __('dashboard.Offer_end_date') }}">
                                            @error('salePrice_end')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>






                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Discount_percentage') }}:
                                            </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control " id="salePrice" name="salePrice"
                                                placeholder="{{ __('dashboard.Discount_percentage') }}  " type="text"
                                                value="{{ old('salePrice') }}" data-parsley-type="number" min="1"
                                                max="100" step="1">
                                            @error('salePrice')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr />
                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Main_Image') }}: <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="dropify" data-height="200" name="image"
                                                id="image" required=""
                                                data-parsley-errors-container="#slErrorContainerimage" />

                                            <div id="slErrorContainerimage">
                                                @error('image')
                                                    <span class="tx-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>




                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Save') }} <i
                                    class="typcn typcn-clipboard"></i></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- /Col -->
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/' . app()->getLocale() . '/file-upload.js') }}">
    </script>
    <!--Internal Fancy uploader js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js') }}"></script>

    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>

    <!--Internal  Datepicker js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>


    <script>
        window.Parsley.addValidator('enddateafter', {
            validateString: function(value, requirement) {
                var startDate = $(requirement).val();
                if (!startDate) return true; // لو الحقل فاضي، ما نتحقق

                var start = new Date(startDate);
                var end = new Date(value);

                return end > start;
            },
            messages: {
                ar: 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
                en: 'End date must be after start date'
            }
        });
        // Datepicker
        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            minDate: 0 // هذا يعني الحد الأدنى هو اليوم
        });
        $('#salePrice_end').attr('data-parsley-enddateafter', '#salePrice_start');

        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });



        window.Parsley.addValidator('mindate', {
            validateString: function(value, minDate) {
                if (!value) return true; // لو فارغ يتجاهل التحقق هنا

                const inputDate = new Date(value);
                const min = new Date(minDate);

                // جعل الوقت في بداية اليوم لتجنب فروقات الوقت
                inputDate.setHours(0, 0, 0, 0);
                min.setHours(0, 0, 0, 0);

                return inputDate >= min;
            },
            messages: {
                en: 'The date must be today or later.',
                ar: 'التاريخ يجب أن يكون اليوم أو بعده.'
            }
        });

        $('a.basicdata_first').click(function() {

            event.preventDefault();
            swal({
                title: "{{ __('dashboard.Please_enter_basic_data_product') }}",
                type: "warning",
                showCancelButton: true,
                showConfirmButton: false,
                confirmButtonClass: "btn-danger",
                cancelButtonText: "{{ __('dashboard.Close') }}",
                closeOnConfirm: false,
                closeOnCancel: true
            });
        });


            function toggleQuantityField() {
        const trackYes = document.getElementById('track_quantity_yes');
        const qtySection = document.getElementById('quantity_section');
        const qtyInput = document.getElementById('quantity');

        if (trackYes.checked) {
            qtySection.style.display = 'block';
            qtyInput.setAttribute('required', '');
            qtyInput.setAttribute('data-parsley-required', 'true');
        } else {
            qtySection.style.display = 'none';
            qtyInput.removeAttribute('required');
            qtyInput.removeAttribute('data-parsley-required');
            qtyInput.value = ''; // امسح القيمة
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleQuantityField();
    });

    // Run on change
    document.getElementById('track_quantity_yes').addEventListener('change', toggleQuantityField);
    document.getElementById('track_quantity_no').addEventListener('change', toggleQuantityField);
    </script>
@endsection
