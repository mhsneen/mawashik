@extends('layouts.admin-login')
@section('title')
    {{ __('dashboard.Create an Account') }}
@endsection

@section('css')
    <style type="text/css">
        #maporder {
            height: 400px;
            width: 100%;
        }
    </style>
    <!-- Sidemenu-respoansive-tabs css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('vendor/mckenziearts/laravel-notify/dist/' . getNotiftcss() . '.css') }}">

    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!---Internal Fileupload css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet"
        type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- The image half -->
            <div class="col-md-5 col-lg-5 col-xl-7 d-none d-md-flex bg-primary-transparent">
                <div class="row wd-100p mx-auto text-center">
                    <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                        <img src="{{ URL::asset('assets/dashboard/img/media/register.png') }}"
                            class="my-auto ht-xl-80p wd-md-100p wd-xl-50p ht-xl-60p mx-auto" alt="logo">
                    </div>
                </div>
            </div>
            <!-- The content half -->
            <div class="col-md-7 col-lg-7 col-xl-5 bg-white">
                <div class="login d-flex align-items-center py-2">
                    <!-- Demo content-->
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                <div class="card-sigin">
                                    <div class="mb-5 d-flex"> <img
                                            src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}"
                                            class="sign-favicon " alt="logo">
                                        <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">&nbsp; </h1>
                                    </div>
                                    <div class="card-sigin">
                                        <div class="main-signup-header">
                                            <h2>{{ __('dashboard.Create an Account') }}</h2>

                                            @include('dashboard.includes.alerts.errors')
                                            @include('dashboard.includes.alerts.success')


                                            <br />
                                            <form action="{{ route('admin.postcreateaccount') }}" method="POST"
                                                data-parsley-validate="" enctype="multipart/form-data" id="createForm">
                                                @csrf


                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Categoriesnew') }}</label> <span
                                                        class="tx-danger">*</span>


                                                    <div class="parsley-select " id="slWrapperapplicationcategory_id">
                                                        <select class="form-control select2"
                                                            data-parsley-class-handler="#slWrapperapplicationcategory_id"
                                                            required=""
                                                            data-parsley-errors-container="#slErrorContainerapplicationcategory_id"
                                                            data-placeholder="{{ __('dashboard.Choose') }}"
                                                            id="applicationcategory_id" name="applicationcategory_id">
                                                            <option value="  ">
                                                                {{ __('dashboard.Choose') }}
                                                            </option>
                                                            @foreach ($applicationcategories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ old('applicationcategory_id') == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>


                                                        <div id="slErrorContainerapplicationcategory_id">
                                                            @error('applicationcategory_id')
                                                                <span class="tx-danger">{{ $message }} </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>



                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Account holder name') }}</label> <span
                                                        class="tx-danger">*</span> <input class="form-control"
                                                        placeholder="{{ __('dashboard.Account holder name') }}"
                                                        type="text" name="name" id="name"
                                                        value="{{ old('name') }}" required="">
                                                    @error('name')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Mobile') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input class="form-control" placeholder="{{ __('dashboard.Mobile') }}"
                                                        type="text" name="mobile" id="mobile"
                                                        value="{{ old('mobile') }}" required data-parsley-type="digits"
                                                        data-parsley-length="[10, 10]">
                                                    @error('mobile')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Email') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input class="form-control" placeholder="{{ __('dashboard.Email') }}"
                                                        type="email" name="email" id="email"
                                                        value="{{ old('email') }}" required data-parsley-type="email">
                                                    @error('email')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">{{ __('dashboard.Restaurant Name') }} ( AR )
                                                        : <span class="tx-danger">*</span></label>


                                                    <input class="form-control tx_R" id="restaurant_name_ar"
                                                        name="restaurant_name_ar"
                                                        placeholder="{{ __('dashboard.Restaurant Name') }} ( AR ) "
                                                        required="" type="text"
                                                        value="{{ old('restaurant_name_ar') }}">
                                                    @error('restaurant_name_ar')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>



                                                <div class="form-group">
                                                    <label class="form-label">{{ __('dashboard.Restaurant Name') }} ( EN )
                                                        : <span class="tx-danger">*</span></label>


                                                    <input class="form-control tx_L" id="restaurant_name_en"
                                                        name="restaurant_name_en"
                                                        placeholder="{{ __('dashboard.Restaurant Name') }} ( EN ) "
                                                        required="" type="text"
                                                        value="{{ old('restaurant_name_en') }}">
                                                    @error('restaurant_name_en')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Logo') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input type="file" class="dropify" name="logo" id="logo"
                                                        required=""
                                                        data-parsley-errors-container="#slErrorContainerlogo" />

                                                    <div id="slErrorContainerlogo">
                                                        @error('logo')
                                                            <span class="tx-danger">{{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Interface image') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input type="file" class="dropify" name="interface_image"
                                                        id="interface_image" required=""
                                                        data-parsley-errors-container="#slErrorContainerinterface_image" />

                                                    <div id="slErrorContainerinterface_image">
                                                        @error('interface_image')
                                                            <span class="tx-danger">{{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Short_Description') }} ( AR )</label>
                                                    <span class="tx-danger">*</span>


                                                    <textarea class="form-control tx_R" placeholder="{{ __('dashboard.Short_Description') }} " rows="5"
                                                        required="" id="description_ar" name="description_ar"> {{ old('description_ar') }}</textarea>

                                                    @error('description_ar')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Short_Description') }} ( EN )</label>
                                                    <span class="tx-danger">*</span>


                                                    <textarea class="form-control tx_L" placeholder="{{ __('dashboard.Short_Description') }} " rows="5"
                                                        required="" id="description_en" name="description_en"> {{ old('description_en') }}</textarea>

                                                    @error('description_en')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <div class="form-group">
                                                    <label class="form-label">{{ __('dashboard.Address') }} ( AR ) : <span
                                                            class="tx-danger">*</span></label>


                                                    <input class="form-control tx_R" id="address_ar" name="address_ar"
                                                        placeholder="{{ __('dashboard.Address') }} ( AR ) "
                                                        required="" type="text" value="{{ old('address_ar') }}">
                                                    @error('address_ar')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>



                                                <div class="form-group">
                                                    <label class="form-label">{{ __('dashboard.Address') }} ( EN ) : <span
                                                            class="tx-danger">*</span></label>


                                                    <input class="form-control tx_L" id="address_en" name="address_en"
                                                        placeholder="{{ __('dashboard.Address') }} ( EN ) "
                                                        required="" type="text" value="{{ old('address_en') }}">
                                                    @error('address_en')
                                                        <span class="tx-danger">{{ $message }} </span>
                                                    @enderror
                                                </div>


                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Address_map') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <div id="maporder"></div>
                                                    <!-- الحقول المخفية -->
                                                    <input type="hidden" name="latitude" id="latitude"
                                                        value="{{ old('latitude') }}">
                                                    <input type="hidden" name="longitude" id="longitude"
                                                        value="{{ old('longitude') }}">



                                                    @error('longitude')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                    @error('latitude')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Password') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input class="form-control" type="password" name="password"
                                                        id="password" value="{{ old('password') }}" required
                                                        data-parsley-minlength="6">
                                                    @error('password')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('dashboard.Confirm_Password') }}</label>
                                                    <span class="tx-danger">*</span>
                                                    <input class="form-control" type="password"
                                                        name="password_confirmation" id="password-confirmation"
                                                        value="{{ old('password_confirmation') }}" required
                                                        data-parsley-equalto="#password">
                                                    @error('password_confirmation')
                                                        <span class="tx-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <br />
                                                <button class="btn btn-main-primary btn-block"
                                                    type="submit">{{ __('dashboard.Create') }}</button>

                                            </form>

                                            <div class="main-signin-footer mt-2">
                                                <p><a href="{{ route('admin.login') }}">{{ __('dashboard.Login') }}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End -->
                </div>
            </div><!-- End -->
        </div>
    </div>
@endsection
@section('js')
    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/js/form-validation-login.js') }}"></script>


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


    <script>
        function initMap() {
            const defaultLatLng = {
                lat: 24.7136,
                lng: 46.6753
            }; // الرياض
            const map = new google.maps.Map(document.getElementById("maporder"), {
                zoom: 11,
                center: defaultLatLng,
            });

            let marker = new google.maps.Marker({
                position: defaultLatLng,
                map: map,
                draggable: true,
            });

            // تحديث الحقول عند سحب marker
            google.maps.event.addListener(marker, 'dragend', function(evt) {
                document.getElementById('latitude').value = evt.latLng.lat();
                document.getElementById('longitude').value = evt.latLng.lng();
            });

            // تغيير موقع marker عند النقر على الخريطة
            google.maps.event.addListener(map, 'click', function(evt) {
                marker.setPosition(evt.latLng);
                document.getElementById('latitude').value = evt.latLng.lat();
                document.getElementById('longitude').value = evt.latLng.lng();
            });

            // دالة للحصول على الموقع الحالي
            function getCurrentLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            map.setCenter(pos);
                            marker.setPosition(pos);
                            document.getElementById('latitude').value = pos.lat;
                            document.getElementById('longitude').value = pos.lng;
                        },
                        function(error) {
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    alert("يرجى السماح للمتصفح باستخدام الموقع لإظهار موقعك الحالي.");
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    alert("لم يتم العثور على موقعك.");
                                    break;
                                case error.TIMEOUT:
                                    alert("انتهت مهلة طلب الموقع.");
                                    break;
                                default:
                                    alert("حدث خطأ أثناء تحديد الموقع.");
                                    break;
                            }
                        }, {
                            timeout: 10000
                        }
                    );
                } else {
                    alert("المتصفح لا يدعم خدمة تحديد الموقع.");
                }
            }

            // أخذ الموقع تلقائيًا عند تحميل الصفحة
            getCurrentLocation();


        }

        // منع إرسال الفورم إذا لم يتم تحديد الموقع
        document.getElementById('createForm').addEventListener('submit', function(e) {
            let lat = document.getElementById('latitude').value;
            let lng = document.getElementById('longitude').value;
            if (!lat || !lng) {
                e.preventDefault();
                alert('يرجى تحديد موقع على الخريطة!');
            }
        });

        window.initMap = initMap;

        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });
    </script>

    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB556JrqytIxxt2hT5hkpLBQdUblve3w5U&language={{ app()->getLocale() }}&callback=initMap">
    </script>

    @notifyJs
@endsection
