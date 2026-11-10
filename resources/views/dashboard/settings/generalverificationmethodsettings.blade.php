@extends('layouts.admin_dashboard')

@section('title')
    {{ __('dashboard.verification_method') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* تحسينات جمالية إضافية للفصل بين الأقسام */
        .settings-card {
            transition: all 0.3s ease;
            border-top: 3px solid #705ec8; /* لون افتراضي أنيق */
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .settings-card .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .sms-theme { border-top-color: #17a2b8; }
        .whatsapp-theme { border-top-color: #28a745; }
        .email-theme { border-top-color: #ffc107; }
        .form-label { font-weight: 500; color: #343a40; }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.verification_method') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <form method="post" data-parsley-validate="" enctype="multipart/form-data" action="{{ route('update.updategeneralverificationmethodsettings') }}">
                @csrf

                <!-- البطاقة الرئيسية: اختيار طريقة التفعيل -->
                <div class="card settings-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="feather feather-sliders list-heading-icon"></i> {{ __('dashboard.verification_method') }}</h5>
                    </div>
                    <div class="card-body">


 <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label mb-md-0">{{ __('dashboard.Registration_Activation_Mode') }}: <span class="tx-danger">*</span></label>
                            </div>
                            <div class="col-md-6">
                                <div class="parsley-select" id="slWrapperregistration_activation_mode">
                                    <select class="form-control select2"
                                            data-parsley-class-handler="#slWrapperregistration_activation_mode"
                                            data-parsley-errors-container="#slErrorContainerregistration_activation_mode"
                                            data-placeholder="{{ __('dashboard.Choose') }}"
                                            id="registration_activation_mode" name="registration_activation_mode" required="">
                                        <option value="">{{ __('dashboard.Choose') }}</option>
                                        <option value="direct" {{ $setting['registration_activation_mode']->plain_value == 'direct' ? 'selected' : '' }}>
                                            {{ __('dashboard.Direct_Registration') }}
                                        </option>
                                        <option value="verification" {{ $setting['registration_activation_mode']->plain_value == 'verification' ? 'selected' : '' }}>
                                            {{ __('dashboard.Registration_Requires_Verification') }}
                                        </option>

                                    </select>
                                    <div id="slErrorContainerregistration_activation_mode">
                                        @error('registration_activation_mode')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
						<br/>
						<br/>






                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label mb-md-0">{{ __('dashboard.verification_method') }}: <span class="tx-danger">*</span></label>
                            </div>
                            <div class="col-md-6">
                                <div class="parsley-select" id="slWrapperverification_method">
                                    <select class="form-control select2"
                                            data-parsley-class-handler="#slWrapperverification_method"
                                            data-parsley-errors-container="#slErrorContainerverification_method"
                                            data-placeholder="{{ __('dashboard.Choose') }}"
                                            id="verification_method" name="verification_method" required="">
                                        <option value="">{{ __('dashboard.Choose') }}</option>
                                        <option value="sms" {{ $setting['verification_method']->plain_value == 'sms' ? 'selected' : '' }}>
                                            {{ __('dashboard.sms') }}
                                        </option>
                                        <option value="whatsapp" {{ $setting['verification_method']->plain_value == 'whatsapp' ? 'selected' : '' }}>
                                            {{ __('dashboard.Whatsapp') }}
                                        </option>
                                        <option value="email" {{ $setting['verification_method']->plain_value == 'email' ? 'selected' : '' }}>
                                            {{ __('dashboard.Email') }}
                                        </option>
                                    </select>
                                    <div id="slErrorContainerverification_method">
                                        @error('verification_method')
                                            <span class="tx-danger">{{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم إعدادات SMS -->
                <div class="card settings-card sms-theme method-section" id="section_sms" style="display: none;">
                    <div class="card-header text-info">
                        <h5 class="card-title mb-0"><i class="main-content-label mg-b-5"></i> {{ __('dashboard.sms_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_provider') }}:</label>
                                <input class="form-control tx_L" id="sms_provider" name="sms_provider" placeholder="{{ __('dashboard.sms_provider') }}" type="text" value="{{ $setting['sms_provider']->plain_value }}">
                                @error('sms_provider') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_api_key') }}:</label>
                                <input class="form-control tx_L" id="sms_api_key" name="sms_api_key" placeholder="{{ __('dashboard.sms_api_key') }}" type="text" value="{{ $setting['sms_api_key']->plain_value }}">
                                @error('sms_api_key') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_sender_id') }}:</label>
                                <input class="form-control tx_L" id="sms_sender_id" name="sms_sender_id" placeholder="{{ __('dashboard.sms_sender_id') }}" type="text" value="{{ $setting['sms_sender_id']->plain_value }}">
                                @error('sms_sender_id') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_username') }}:</label>
                                <input class="form-control tx_L" id="sms_username" name="sms_username" placeholder="{{ __('dashboard.sms_username') }}" type="text" value="{{ $setting['sms_username']->plain_value }}">
                                @error('sms_username') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_password') }}:</label>
                                <input class="form-control tx_L" id="sms_password" name="sms_password" placeholder="{{ __('dashboard.sms_password') }}" type="password" value="{{ $setting['sms_password']->plain_value }}">
                                @error('sms_password') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.sms_base_url') }}:</label>
                                <input class="form-control tx_L" id="sms_base_url" name="sms_base_url" placeholder="{{ __('dashboard.sms_base_url') }}" type="text" value="{{ $setting['sms_base_url']->plain_value }}">
                                @error('sms_base_url') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم إعدادات الواتساب -->
                <div class="card settings-card whatsapp-theme method-section" id="section_whatsapp" style="display: none;">
                    <div class="card-header text-success">
                        <h5 class="card-title mb-0">{{ __('dashboard.whatsapp_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_provider') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_provider" name="whatsapp_provider" placeholder="{{ __('dashboard.whatsapp_provider') }}" type="text" value="{{ $setting['whatsapp_provider']->plain_value }}">
                                @error('whatsapp_provider') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_api_url') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_api_url" name="whatsapp_api_url" placeholder="{{ __('dashboard.whatsapp_api_url') }}" type="text" value="{{ $setting['whatsapp_api_url']->plain_value }}">
                                @error('whatsapp_api_url') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_token') }} :</label>
                                <input class="form-control tx_L" id="whatsapp_token" name="whatsapp_token" placeholder="{{ __('dashboard.whatsapp_token') }}" type="text" value="{{ $setting['whatsapp_token']->plain_value }}">
                                @error('whatsapp_token') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_instance_id') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_instance_id" name="whatsapp_instance_id" placeholder="{{ __('dashboard.whatsapp_instance_id') }}" type="text" value="{{ $setting['whatsapp_instance_id']->plain_value }}">
                                @error('whatsapp_instance_id') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_phone_number') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_phone_number" name="whatsapp_phone_number" placeholder="{{ __('dashboard.whatsapp_phone_number') }}" type="text" value="{{ $setting['whatsapp_phone_number']->plain_value }}">
                                @error('whatsapp_phone_number') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_phone_number_id') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_phone_number_id" name="whatsapp_phone_number_id" placeholder="{{ __('dashboard.whatsapp_phone_number_id') }}" type="text" value="{{ $setting['whatsapp_phone_number_id']->plain_value }}">
                                @error('whatsapp_phone_number_id') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.whatsapp_business_account_id') }}:</label>
                                <input class="form-control tx_L" id="whatsapp_business_account_id" name="whatsapp_business_account_id" placeholder="{{ __('dashboard.whatsapp_business_account_id') }}" type="text" value="{{ $setting['whatsapp_business_account_id']->plain_value }}">
                                @error('whatsapp_business_account_id') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم إعدادات البريد الإلكتروني -->
                <div class="card settings-card email-theme method-section" id="section_email" style="display: none;">
                    <div class="card-header text-warning">
                        <h5 class="card-title mb-0">{{ __('dashboard.email_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_mailer') }}:</label>
                                <input class="form-control tx_L" id="mail_mailer" name="mail_mailer" placeholder="{{ __('dashboard.mail_mailer') }}" type="text" value="{{ $setting['mail_mailer']->plain_value }}">
                                @error('mail_mailer') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_host') }}:</label>
                                <input class="form-control tx_L" id="mail_host" name="mail_host" placeholder="{{ __('dashboard.mail_host') }}" type="text" value="{{ $setting['mail_host']->plain_value }}">
                                @error('mail_host') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_port') }}:</label>
                                <input class="form-control tx_L" id="mail_port" name="mail_port" placeholder="{{ __('dashboard.mail_port') }}" type="text" value="{{ $setting['mail_port']->plain_value }}">
                                @error('mail_port') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_username') }}:</label>
                                <input class="form-control tx_L" id="mail_username" name="mail_username" placeholder="{{ __('dashboard.mail_username') }}" type="text" value="{{ $setting['mail_username']->plain_value }}">
                                @error('mail_username') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_password') }}:</label>
                                <input class="form-control tx_L" id="mail_password" name="mail_password" placeholder="{{ __('dashboard.mail_password') }}" type="password" value="{{ $setting['mail_password']->plain_value }}">
                                @error('mail_password') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_encryption') }}:</label>
                                <input class="form-control tx_L" id="mail_encryption" name="mail_encryption" placeholder="{{ __('dashboard.mail_encryption') }}" type="text" value="{{ $setting['mail_encryption']->plain_value }}">
                                @error('mail_encryption') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_from_address') }}:</label>
                                <input class="form-control tx_L" id="mail_from_address" name="mail_from_address" placeholder="{{ __('dashboard.mail_from_address') }}" type="text" value="{{ $setting['mail_from_address']->plain_value }}">
                                @error('mail_from_address') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.mail_from_name') }}:</label>
                                <input class="form-control tx_L" id="mail_from_name" name="mail_from_name" placeholder="{{ __('dashboard.mail_from_name') }}" type="text" value="{{ $setting['mail_from_name']->plain_value }}">
                                @error('mail_from_name') <span class="tx-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- زر الحفظ والإرسال -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <button class="btn btn-success-gradient btn-lg px-5" type="submit">
                            {{ __('dashboard.Edit') }} <i class="typcn typcn-clipboard ml-1"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <!--Internal Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // تفعيل مكتبة Select2
            $('.select2').select2({
                placeholder: '{{ __('dashboard.Choose') }}',
                searchInputPlaceholder: '{{ __('dashboard.Search') }}',
                width: '100%'
            });

            // دالة لإظهار وإخفاء الأقسام بناءً على القيمة المختارة
            function toggleSections() {
                var selectedMethod = $('#verification_method').val();

                // إخفاء كل الأقسام أولاً بحركة ناعمة
                $('.method-section').hide();

                // إظهار القسم المطلوب فقط
                if (selectedMethod === 'sms') {
                    $('#section_sms').fadeIn(400);
                } else if (selectedMethod === 'whatsapp') {
                    $('#section_whatsapp').fadeIn(400);
                } else if (selectedMethod === 'email') {
                    $('#section_email').fadeIn(400);
                }
            }

            // تشغيل الدالة عند تحميل الصفحة لمعرفة الخيار المختار مسبقاً من قاعدة البيانات
            toggleSections();

            // تشغيل الدالة عند تغيير خيار القائمة المنسدلة
            $('#verification_method').on('change', function() {
                toggleSections();
            });
        });
    </script>
@endsection
