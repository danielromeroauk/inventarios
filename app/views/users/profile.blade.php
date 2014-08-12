@extends('layouts.master')

@section('head')

    {{ HTML::style('css/flipclock.css') }}

    {{ HTML::style('fonts/chelaone.css') }}
    {{-- HTML::style('http://fonts.googleapis.com/css?family=Chela+One') --}}

    <!--{{ HTML::script('js/flipclock/libs/prefixfree.min.js') }}
    {{ HTML::script('js/flipclock/flipclock.min.js') }}
    {{ HTML::script('js/flipclock-customer.js') }}-->

    <style>
    h1 {
        font-family: "Chela One";
        font-size: 2.7em;
    }
    </style>

@stop

@section('content')

	<div class="alert-success well">
		Hola {{ Auth::user()->name }}, te encuentras asignado a la sucursal <strong>{{ Auth::user()->roles()->first()->branch->name }}</strong>
	</div>

    <!-- <h1 class="well-lg">Tiempo restante para uso gratuito por donación</h1>
    <div id="mi-reloj"></div> -->

@stop
