@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Edit') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

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
                <h4 class="content-title mb-0 my-auto">{{ __($namesection) }} </h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
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
    <!-- row -->
    <div class="row row-sm">


        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" data-parsley-validate="" enctype="multipart/form-data"
                        action ="{{ route($routeName . '.update', $item->id) }}">
                        @csrf

                        <input type="hidden" id="id" name="id" value="{{ $item->id }}" />



                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('dashboard.Discount Code') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_L" id="code" name="code" type="text"
                                                style="text-align: center" value="{{ $item->code }}" readonly>
                                            @error('code')
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
                                            <label class="form-label">{{ __('dashboard.Discount Percentage') }}
                                                : <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_L " id="discount_percentage"
                                                style="text-align: center" name="discount_percentage"
                                                placeholder="{{ __('dashboard.Integer') }}" required type="number"
                                                value="{{ $item->discount_percentage }}" data-parsley-type="integer"
                                                data-parsley-min="1" data-parsley-max="50"
                                                data-parsley-error-message="النسبة يجب أن تكون رقم صحيح بين 1 و 50">
                                            @error('discount_percentage')
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
                                            <label class="form-label">{{ __('dashboard.Start Date') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R fc-datepicker" id="start_date" name="start_date"
                                                type="text"
                                                value="{{ $item->start_date ? $item->start_date->format('Y-m-d') : '' }}"
                                                required >
                                            @error('start_date')
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
                                            <label class="form-label">{{ __('dashboard.End Date') }}
                                                : <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R fc-datepicker" id="end_date" name="end_date"
                                                type="text"
                                                value="{{ $item->end_date ? $item->end_date->format('Y-m-d') : '' }}"
                                                required>
                                            @error('end_date')
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
                                            <label class="form-label">{{ __('dashboard.Number of uses') }}
                                                : </label>

                                        </div>
                                        <div class="col-md-8">
                                            <div class="parsley-select " id="slWrapperusage_type">
                                                <select class="form-control select2"
                                                    data-parsley-class-handler="#slWrapperusage_type"
                                                    data-parsley-errors-container="#slErrorContainerusage_type"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="usage_type"
                                                    name="usage_type" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>

                                                    <option value="once"
                                                        {{ $item->usage_type == 'once' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Single-use') }}
                                                    </option>


                                                    <option value="multiple"
                                                        {{ $item->usage_type == 'multiple' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Multi-use') }}
                                                    </option>



                                                    <option value="limited"
                                                        {{ $item->usage_type == 'limited' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Specific number') }}
                                                    </option>

                                                </select>


                                                <div id="slErrorContainerusage_type">
                                                    @error('usage_type')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group " id="max_usage_group" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('dashboard.Number of uses') }}
                                                : </label>

                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" name="max_usage" id="max_usage" class="form-control"
                                                min="1" value="{{ $item->max_usage }}"
                                                placeholder="{{ __('dashboard.Number of uses') }}">

                                            @error('max_usage')
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
                                            <label class="form-label">{{ __('dashboard.Stopped') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-8">


                                            <div class="parsley-select " id="slWrapperis_active">
                                                <select class="form-control select2"
                                                    data-parsley-class-handler="#slWrapperis_active"
                                                    data-parsley-errors-container="#slErrorContaineris_active"
                                                    data-placeholder="{{ __('dashboard.Choose') }}" id="is_active"
                                                    name="is_active" required="">
                                                    <option value="  ">
                                                        {{ __('dashboard.Choose') }}
                                                    </option>




                                                        <option value="1" {{$item->is_active == '1' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Yes') }}
                                                    </option>
                                                    <!-- ✅ القيمة 0 تعني متوقف (No) -->
                                                      <option value="0" {{$item->is_active == '0' ? 'selected' : '' }}>
                                                        {{ __('dashboard.No') }}
                                                    </option>




                                                </select>
                                                <div id="slErrorContaineris_active">
                                                    @error('is_active')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

















                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Edit') }} <i
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

        $('#end_date').attr('data-parsley-enddateafter', '#start_date');

        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });




        $(document).ready(function() {



            const usageTypeSelect = $('#usage_type');
            const maxUsageGroup = $('#max_usage_group');
            const maxUsageInput = $('#max_usage');

            function toggleMaxUsage() {
                if (usageTypeSelect.val() === 'limited') {
                    maxUsageGroup.show();
                    maxUsageInput.attr('required', 'required');
                } else {
                    maxUsageGroup.hide();
                    maxUsageInput.removeAttr('required');
                    maxUsageInput.val('');
                }
                maxUsageInput.parsley().reset();
            }

            usageTypeSelect.on('change', toggleMaxUsage);

            toggleMaxUsage();
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
    </script>
@endsection
