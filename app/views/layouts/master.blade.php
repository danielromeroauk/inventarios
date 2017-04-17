<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title>@if(isset($title)) {{ $title .' - ' }} @endif Inventarios</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if(Config::get('app.entorno') == 'local')
		{{ HTML::style('bootstrap-3.0.0/css/bootstrap.min.css') }}
		{{ HTML::script('js/jquery.min.js') }}
		{{ HTML::script('bootstrap-3.0.0/js/bootstrap.min.js') }}
	@else
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" />
		<!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet" /> -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script>window.jQuery || document.write(unescape('%3Cscript src="{{ 'js/jquery.min.js' }}"%3E%3C/script%3E'));
		</script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	@endif

	{{ HTML::style('css/jumbotron.css') }}
	{{ HTML::style('css/inventarios.css') }}

	{{ HTML::script('js/jconfirmaction.jquery.js') }}
	{{ HTML::script('js/contador.js?year=2018') }}
	{{ HTML::script('js/inventarios.js') }}

	@yield('head')

</head>
<body>

	@include('menu')

	@if(Session::has('mensajewell'))
		<div class="well">
			{{ Session::get('mensajewell') }}
		</div>
	@endif

	<div class="container">

		@include('plugins.status')

		@yield('content')

	</div>

	@include('analyticstracking')
	
</body>
</html>