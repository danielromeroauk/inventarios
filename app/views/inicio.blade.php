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
				<th>Acción</th>
	    	</tr>
	    	<tr>
	    		<td>Compras</td>
	    		<td class="center">{{ Purchase::where('status', '=', 'pendiente')->count() }}</td>
	    		<td>
	    			<a href="{{ url('purchases/filter-by-status?estado=pendiente') }}">
	    				<span class="glyphicon glyphicon-search"></span>
	    				Ver informe
	    			</a>
	    		</td>
	    	</tr>
	    	<tr>
	    		<td>Ventas</td>
	    		<td class="center">{{ Sale::where('status', '=', 'pendiente')->count() }}</td>
	    		<td>
	    			<a href="{{ url('sales/filter-by-status?estado=pendiente') }}">
	    				<span class="glyphicon glyphicon-search"></span>
	    				Ver informe
	    			</a>
	    		</td>
	    	</tr>
	    	<tr>
	    		<td>Daños</td>
	    		<td class="center">{{ Damage::where('status', '=', 'pendiente')->count() }}</td>
	    		<td>
	    			<a href="{{ url('damages/filter-by-status?estado=pendiente') }}">
	    				<span class="glyphicon glyphicon-search"></span>
	    				Ver informe
	    			</a>
	    		</td>
	    	</tr>
	    	<tr>
	    		<td>Rotaciones</td>
	    		<td class="center">{{ Rotation::where('status', '=', 'pendiente')->count() }}</td>
	    		<td>
	    			<a href="{{ url('rotations/filter-by-status?estado=pendiente') }}">
	    				<span class="glyphicon glyphicon-search"></span>
	    				Ver informe
	    			</a>
	    		</td>
	    	</tr>
	    </table>

	</div>

@stop