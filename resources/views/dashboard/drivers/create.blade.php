@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Add_New') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!---Internal Fileupload css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />

    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __($namesection) }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Add_New') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
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
                        action="{{ route($routeName . '.store') }}">
                        @csrf

                        {{-- ===== القسم الأول: البيانات الشخصية ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-user text-primary"></i>
                            {{ __('dashboard.Personal_Information') }}
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Name') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R" id="name" name="name" placeholder="{{ __('dashboard.Name') }}" required="" type="text" value="{{ old('name') }}">
                                            @error('name')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Email') }} : </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control tx_R" id="email" name="email" placeholder="{{ __('dashboard.Email') }}" type="text" value="{{ old('email') }}" data-parsley-type="email">
                                            @error('email')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Efficiency_Status') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row mg-t-10">
                                                <div class="col-lg-4">
                                                    <label class="rdiobox">
                                                        <input name="is_active" type="radio" value="1" required="" @if (old('is_active', '1') == '1') checked @endif>
                                                        <span>{{ __('dashboard.effective') }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label class="rdiobox">
                                                        <input name="is_active" type="radio" value="0" required="" @if (old('is_active') == '0') checked @endif>
                                                        <span>{{ __('dashboard.Inactive') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            @error('is_active')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />

                        {{-- ===== القسم الثاني: بيانات تسجيل الدخول ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-lock text-info"></i>
                            {{ __('dashboard.Login_information_application') }}
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Mobile') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control" id="mobile" name="mobile" placeholder="{{ __('dashboard.Mobile') }}" required="" type="text" value="{{ old('mobile') }}" data-parsley-type="number" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');">
                                            @error('mobile')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <hr />

                        {{-- ===== القسم الثالث: الصورة الشخصية ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-image text-success"></i>
                            {{ __('dashboard.Image') }}
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Image') }} : </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="dropify" data-height="200" name="image" id="image" data-max-file-size="5M" data-parsley-errors-container="#slErrorContainerimage" />
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

                        <hr />

                        {{-- ===== القسم الرابع: وثائق المندوب (الهوية + الرخصة) — حسب البند الرابع من المواصفات ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-id-card text-warning"></i>
                            {{ __('dashboard.Driver_Documents') }}
                            <small class="text-muted d-block tx-12">{{ __('dashboard.Driver_Documents_Hint') }}</small>
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            {{-- صورة الهوية --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.ID_Image') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="dropify" data-height="200" required="" name="id_image" id="id_image" data-max-file-size="5M" data-parsley-errors-container="#slErrorContainerIdImage" />
                                            <div id="slErrorContainerIdImage" >
                                                @error('id_image')
                                                    <span class="tx-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- صورة الرخصة --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.License_Image') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="dropify" data-height="200" required="" name="license_image" id="license_image" data-max-file-size="5M" data-parsley-errors-container="#slErrorContainerLicenseImage" />
                                            <div id="slErrorContainerLicenseImage" >
                                                @error('license_image')
                                                    <span class="tx-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />

                        {{-- ===== القسم الخامس: الموافقة على الشروط ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-check-circle text-info"></i>
                            {{ __('dashboard.Terms_And_Conditions') }}
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="ckbox mg-b-0">
                                        <input type="checkbox" name="terms_accepted" value="1" required="" @if (old('terms_accepted')) checked @endif>
                                        <span>{{ __('dashboard.Terms_Accepted') }} <span class="tx-danger">*</span></span>
                                    </label>
                                    <small class="text-muted d-block mt-2 tx-12">{{ __('dashboard.Terms_Accepted_Hint') }}</small>
                                    @error('terms_accepted')
                                        <span class="tx-danger">{{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr />

                        {{-- ===== القسم السادس: إعدادات الاعتماد والعمولة ===== --}}
                        <div class="main-content-label mg-b-5">
                            <i class="fa fa-cog text-purple"></i>
                            {{ __('dashboard.Driver_Approval_Settings') }}
                        </div>
                        <br />

                        <div class="row row-xs formgroup-wrapper">
                            {{-- حالة الاعتماد --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Driver_Status') }} : </label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="form-control select2" name="driver_status">
                                                <option value="pending"   @if (old('driver_status', 'pending') == 'pending')   selected @endif>{{ __('dashboard.Driver_Status_Pending') }}</option>
                                                <option value="approved"  @if (old('driver_status') == 'approved')  selected @endif>{{ __('dashboard.Driver_Status_Approved') }}</option>
                                                <option value="rejected"  @if (old('driver_status') == 'rejected')  selected @endif>{{ __('dashboard.Driver_Status_Rejected') }}</option>
                                                <option value="suspended" @if (old('driver_status') == 'suspended') selected @endif>{{ __('dashboard.Driver_Status_Suspended') }}</option>
                                            </select>
                                            @error('driver_status')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- نسبة العمولة --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Driver_Commission_Rate') }} : </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="driver_commission_rate" min="0" max="100" step="0.01" value="{{ old('driver_commission_rate') }}" placeholder="{{ __('dashboard.Driver_Commission_Rate_Placeholder') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1 tx-12">{{ __('dashboard.Driver_Commission_Rate_Hint') }}</small>
                                            @error('driver_commission_rate')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button class="btn btn-success-gradient mg-10" type="submit">
                                {{ __('dashboard.Save') }} <i class="typcn typcn-clipboard"></i>
                            </button>
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
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/' . app()->getLocale() . '/file-upload.js') }}"></script>
    <!--Internal Fancy uploader js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js') }}"></script>
    <!--Internal Parsley.min js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>

    <!--Internal Sweet-Alert js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>

    <script type="text/javascript">
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });

        // تنشيط Dropify للحقول الجديدة
        $('.dropify').dropify({
            messages: {
                'default': '{{ __('dashboard.Click_or_drag_to_upload') }}',
                'replace': '{{ __('dashboard.Click_or_drag_to_replace') }}',
                'remove':  '{{ __('dashboard.Remove') }}',
                'error':   '{{ __('dashboard.File_too_large') }}'
            }
        });
    </script>
@endsection
