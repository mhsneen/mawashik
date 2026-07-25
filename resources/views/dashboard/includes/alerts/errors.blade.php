@if(Session::has('error'))
<div class="alert alert-danger mb-0" role="alert">
	<span class="alert-inner--icon"><i class="fe fe-slash"></i></span>
	<span class="alert-inner--text">{{ Session::get('error') }}</span>
</div>
<br/>
@endif