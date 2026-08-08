<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />
    @include('dashboard.includes.head')
</head>

<body class="main-body app sidebar-mini">
    <!-- Loader -->
    <div id="global-loader">
        <img src="{{ URL::asset('assets/dashboard/img/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    @include('dashboard.includes.main-sidebar')
   




    <!-- main-content -->
    <div class="main-content app-content">
        @include('dashboard.includes.main-header')
        <!-- container -->
        <div class="container-fluid">
            @yield('page-header')
            @yield('content')
            @include('dashboard.includes.sidebar')
            @include('dashboard.includes.models')
            @include('dashboard.includes.footer')
            @include('dashboard.includes.footer-scripts')



<!-- المكتبات المطلوبة -->

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
 <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>





</body>

</html>
