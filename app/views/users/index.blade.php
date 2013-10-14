@extends('layouts.master')

@section('head')

	{{ HTML::style('css/signin.css') }}

@stop

@section('content')

	<div class="container">

	  {{ Form::open(array('url' => 'users/index', 'class' => 'form-signin')) }}
        <h2 class="form-signin-heading">¿Quién eres?</h2>
		{{ Form::email('email', (isset($email) ? $email : ''), array('class' => 'form-control', 'placeholder' => 'Email', 'required')) }}
		{{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password', 'required')) }}
		{{ Form::submit('Entrar', array('class' => 'btn btn-lg btn-primary btn-block')) }}
	  {{ Form::close() }}

    </div> <!-- /container -->

@stop
