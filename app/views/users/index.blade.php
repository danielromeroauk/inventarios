@extends('layouts.master')

@section('content')

	<div class="row">
		<div class="span4 offset4">

			<div class="well">
				<legend>Por favor, identifíquese</legend>
				{{ Form::open(array('url' => 'users/index')) }}
					{{ Form::email('email', (isset($email) ? $email : ''), array('class' => 'span3', 'placeholder' => 'Email', 'required')) }}
					{{ Form::password('password', array('class' => 'span3', 'placeholder' => 'Password', 'required')) }}
					{{ Form::submit('Entrar', array('class' => 'btn btn-success')) }}
					{{ HTML::link('users/register', 'Registrar', array('class' => 'btn btn-primary')) }}
				{{ Form::close() }}
			</div>

		</div>
	</div>

@stop
