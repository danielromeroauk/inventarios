@extends('layouts.master')

@section('content')
	<div class="alert-success well">
		Bienvenid@ a tu perfil {{ Auth::user()->name }}
	</div>
@stop