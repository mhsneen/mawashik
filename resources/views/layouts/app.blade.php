<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>

<head>

    @include('site.includes.head')
</head>

<body>

    <div class="pageContainer">

        @include('site.includes.main-header')



        @yield('content')


        @include('site.includes.footer')

    </div>

    @include('site.includes.footer-scripts')

</body>

</html>
