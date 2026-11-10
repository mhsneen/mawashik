<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		@include('dashboard.includes.head')
	</head>

	<body class="main-body bg-primary-transparent">

		<!-- /Loader -->
		@yield('content')
	</body>
</html>