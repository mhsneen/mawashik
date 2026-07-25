@extends('layouts.admin_dashboard')

@section('title')
    {{ __('dashboard.Working_days_and_times') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/TimePicki-master/css/timepicki.css') }}" rel="stylesheet">
    <style>
        .custom-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
        }
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('page-header')
    <!-- Breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex align-items-center">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Working_days_and_times') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 ml-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">

            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <form method="post" data-parsley-validate="" enctype="multipart/form-data" action="{{ route('update.updategeneraltimesdayswork') }}">
                @csrf

                <!-- 1. General Setting -->
                <div class="card mb-4 shadow-sm">
                    <div class="custom-card-header">
                        <h5 class="form-section-title"><i class="fas fa-cog mr-2"></i> {{ __('dashboard.General_Settings') }} </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 d-flex align-items-center">
                                <label class="form-label mb-0 font-weight-bold">
                                    {{ __('dashboard.open_work') }} <span class="tx-danger">*</span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control select2" id="open_work" name="open_work" required>
                                    <option value="0" @if ($setting['open_work']->plain_value == 0) selected @endif>
                                        {{ __('dashboard.No') }}
                                    </option>
                                    <option value="1" @if ($setting['open_work']->plain_value == 1) selected @endif>
                                        {{ __('dashboard.Yes') }}
                                    </option>
                                </select>
                                @error('open_work')
                                    <span class="tx-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Days & Schedule -->
                <div class="card mb-4 shadow-sm">
                    <div class="custom-card-header">
                        <h5 class="form-section-title"><i class="far fa-calendar-alt mr-2"></i> {{ __('dashboard.days_work') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">{{ __('dashboard.Enable') }} </th>
                                        <th style="width: 200px;">{{ __('dashboard.Day') }} </th>
                                        <th> {{ __('dashboard.Start_time') }}</th>
                                        <th> {{ __('dashboard.End_time') }}</th>
                                </thead>
                                <tbody>
                                    @foreach ($days as $key => $label)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <label class="ckbox mb-0 justify-content-center">
                                                    <input type="checkbox" class="day-checkbox" data-day="{{ $key }}" name="days_work[]" value="{{ $key }}" {{ in_array($key, $selectedDays) ? 'checked' : '' }}>
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td class="align-middle font-weight-bold text-primary">{{ $label }}</td>
                                            <td>
                                                <input type="text" class="form-control timepicker start_work_{{ $key }}" name="start_work[{{ $key }}]" value="{{ $startWork[$key] ?? '' }}" {{ !in_array($key, $selectedDays) ? 'disabled' : '' }} placeholder="00:00">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control timepicker end_work_{{ $key }}" name="end_work[{{ $key }}]" value="{{ $endWork[$key] ?? '' }}" {{ !in_array($key, $selectedDays) ? 'disabled' : '' }} placeholder="00:00">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('days_work')
                            <span class="tx-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- 3. Closing Messages -->
                <div class="card mb-4 shadow-sm">
                    <div class="custom-card-header">
                        <h5 class="form-section-title"><i class="far fa-envelope mr-2"></i>  {{ __('dashboard.Out_of_working_hours_closure_messages') }} </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Arabic Message -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">{{ __('dashboard.Closing message') }} (AR)</label>
                                <textarea class="form-control" id="out_of_working_hours_message_ar" name="out_of_working_hours_message_ar" rows="4">@if (isset($setting['out_of_working_hours_message']->translate('ar')->value)){{ $setting['out_of_working_hours_message']->translate('ar')->value }}@endif</textarea>
                                @error('out_of_working_hours_message_ar')
                                    <span class="tx-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- English Message -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">{{ __('dashboard.Closing message') }} (EN)</label>
                                <textarea class="form-control" id="out_of_working_hours_message_en" name="out_of_working_hours_message_en" rows="4">@if (isset($setting['out_of_working_hours_message']->translate('en')->value)){{ $setting['out_of_working_hours_message']->translate('en')->value }}@endif</textarea>
                                @error('out_of_working_hours_message_en')
                                    <span class="tx-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <button class="btn btn-success-gradient btn-lg pd-x-30" type="submit">
                            <i class="typcn typcn-clipboard mr-1"></i> {{ __('dashboard.Edit') }}
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('js')
    <!-- Internal Parsley js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Select2 js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <!-- Internal TimePicki js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/TimePicki-master/js/timepicki.js') }}"></script>
    <!-- CKEditor -->
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Select2 Init
            $('.select2').select2({
                placeholder: '{{ __('dashboard.Choose') }}',
                searchInputPlaceholder: '{{ __('dashboard.Search') }}',
                width: '100%'
            });

            // Timepicker Init
            $('.timepicker').timepicki({
            });

            // Toggle Input States via Checkbox
            $('.day-checkbox').on('change', function() {
                let day = $(this).data('day');
                let isChecked = $(this).is(':checked');

                if (isChecked) {
                    $('.start_work_' + day + ', .end_work_' + day).prop('disabled', false);
                } else {
                    $('.start_work_' + day + ', .end_work_' + day).prop('disabled', true).val('');
                }
            });

            // CKEditor Configs
            CKEDITOR.replace('out_of_working_hours_message_ar', {
                language: 'ar',
                filebrowserUploadMethod: 'form',
                filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
                filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files'
            });

            CKEDITOR.replace('out_of_working_hours_message_en', {
                language: 'en',
                filebrowserUploadMethod: 'form',
                filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
                filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=files'
            });
        });
    </script>
    @include('ckfinder::setup')
@endsection
