@extends('layouts.master')

@section('content')

	<div class="col-md-3">
		<img src="{{url('img/inventarios.jpg')}}" alt="inventarios" class="img-responsive" />
	</div>

	<div class="col-md-6">

		<h1>Remisiones pendientes</h1>

		<table class="table table-striped table-hover table-bordered">
			<tr>
				<th>Tipo</th>
				<th class="center">Cantidad</th>
				@if (Auth::check())
					<th>Acción</th>
				@endif
			</tr>
			<tr>
				<td>Compras</td>
				<td class="center">{{ Purchase::where('status', '=', 'pendiente')->count() }}</td>
				@if (Auth::check())
					<td>
						<a href="{{ url('purchases/filter-by-status?estado=pendiente') }}">
							<span class="glyphicon glyphicon-search"></span>
							Ver informe
						</a>
					</td>
				@endif
			</tr>
			<tr>
				<td>Ventas</td>
				<td class="center">{{ Sale::where('status', '=', 'pendiente')->count() }}</td>
				@if (Auth::check())
					<td>
						<a href="{{ url('sales/filter-by-status?estado=pendiente') }}">
							<span class="glyphicon glyphicon-search"></span>
							Ver informe
						</a>
					</td>
				@endif
			</tr>
			<tr>
				<td>Daños</td>
				<td class="center">{{ Damage::where('status', '=', 'pendiente')->count() }}</td>
				@if (Auth::check())
					<td>
						<a href="{{ url('damages/filter-by-status?estado=pendiente') }}">
							<span class="glyphicon glyphicon-search"></span>
							Ver informe
						</a>
					</td>
				@endif
			</tr>
			<tr>
				<td>Rotaciones</td>
				<td class="center">{{ Rotation::where('status', 'like', 'pendiente%')->count() }}</td>
				@if (Auth::check())
					<td>
						<a href="{{ url('rotations/filter-by-status?estado=pendiente%') }}">
							<span class="glyphicon glyphicon-search"></span>
							Ver informe
						</a>
					</td>
				@endif
			</tr>
		</table>

	</div>

@stop