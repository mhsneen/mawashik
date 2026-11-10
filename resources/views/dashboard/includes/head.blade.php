<!-- Title -->
<title> @yield('title') </title>
<!-- Favicon -->

<link rel="icon" href="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}" type="image/x-icon"/>

<!-- Icons css -->
<link href="{{URL::asset('assets/dashboard/css/icons.css')}}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/dashboard/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/dashboard/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{URL::asset('assets/dashboard/'.getFolderAssestLocale().'/sidemenu.css')}}">
@yield('css')
<!--- Style css -->
<link href="{{URL::asset('assets/dashboard/'.getFolderAssestLocale().'/style.css')}}" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{URL::asset('assets/dashboard/'.getFolderAssestLocale().'/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/dashboard/'.getFolderAssestLocale().'/skin-modes.css')}}" rel="stylesheet">
