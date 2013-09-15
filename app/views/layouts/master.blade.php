<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>@if(isset($title)) {{ $title }} @else {{ 'Inventarios' }} @endif</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	{{ HTML::style( 'css/bootstrap.min.css') }}
	{{ HTML::style( 'css/jumbotron.css') }}
	{{ HTML::style( 'css/inventarios.css') }}

</head>
<body>
	@include('menu')
	<div class="container">
		@include('plugins.status')
		@yield('content')
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write(unescape('%3Cscript src="{{ 'js/jquery.min.js' }}"%3E%3C/script%3E'));
	</script>
	{{ HTML::script('js/bootstrap.min.js') }}
	{{ HTML::script('js/jconfirmaction.jquery.js') }}
	{{ HTML::script('js/inventarios.js') }}

</body>
</html>