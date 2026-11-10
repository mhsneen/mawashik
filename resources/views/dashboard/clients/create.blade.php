@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }} - {{ __('dashboard.Add_New') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
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
@endsection
@section('content')
    <div class="row row-sm">
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

                        <!--
    <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Password') }} : <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input class="form-control" id="password" name="password" placeholder="{{ __('dashboard.Password') }}" required="" type="password" autocomplete="new-password">
                                            @error('password')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        -->
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

                        {{-- ===== القسم الرابع: الموافقة على الشروط ===== --}}
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

                        <div class="card-footer text-center">
                            <button class="btn btn-success-gradient mg-10" type="submit">
                                {{ __('dashboard.Save') }} <i class="typcn typcn-clipboard"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fileuploads/js/' . app()->getLocale() . '/file-upload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy-uploader.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
    <script type="text/javascript">
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
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
