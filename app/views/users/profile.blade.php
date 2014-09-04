@extends('layouts.master')

@section('content')

	<div class="alert-success well">
		Hola {{ Auth::user()->name }}, te encuentras asignado a la sucursal <strong>{{ Auth::user()->roles()->first()->branch->name }}</strong>
	</div>

@stop
