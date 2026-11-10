
@if(Session::has('success'))
<div class="alert alert-success" role="alert">
	<span class="alert-inner--icon"><i class="fe fe-thumbs-up"></i></span>
	<span class="alert-inner--text">{{ Session::get('success') }}</span>
</div>
<br/>
@endif