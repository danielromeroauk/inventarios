@extends('layouts.master')

@section('content')

    <div class="well">
        <h1>

            Movimientos de {{ $articulo->name }} <br />

            <small>Desde {{ Input::get('fecha1') }} hasta {{ Input::get('fecha2') }}</small>

            {{ Form::open(array('url' => 'articles/excel-con-movimientos/'. $articulo->id, 'method' => 'get', 'class' => 'form-inline')) }}

                <input type="hidden" name="fecha1" value="{{ Input::get('fecha1') }}">
                <input type="hidden" name="fecha2" value="{{ Input::get('fecha2') }}">

                <button class="btn btn-success btn-sm" type="submit">Generar en excel</button>

            {{ Form::close() }}

        </h2>
    </div>

    <table class="table table-hover table-bordered datos">
        <thead>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Sucursal</th>
            <th class="right">Cantidad</th>
            <th>Estado</th>
            <th>Comentario del remisionero</th>
            <th>Nota reciente</th>
            <th>Acción</th>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
                @if($movimiento['estado'] == 'cancelado')
                    <tr class="warning">

                @elseif($movimiento['estado'] == 'pendiente')
                    <tr class="danger">

                @elseif($movimiento['estado'] == 'finalizado')
                    <tr class="default">

                @else
                    <tr>

                @endif

                    <td>{{ $movimiento['fecha'] }}</td>
                    <td>{{ ucwords($movimiento['tipo']) }}</td>
                    <td>{{ $movimiento['sucursal'] }}</td>
                    <td class="right">{{ $movimiento['cantidad'] }}</td>
                    <td>{{ $movimiento['estado'] }}</td>
                    <td>{{ $movimiento['comentario'] }}</td>
                    <td>{{ $movimiento['nota'] }}</td>
                    <td>
                        <span class="glyphicon glyphicon-search"></span>
                        @if($movimiento['tipo'] == 'compra')
                            {{ HTML::link('purchases/items/'. $movimiento['id'], 'Ver detalles') }}
                        @elseif($movimiento['tipo'] == 'venta')
                            {{ HTML::link('sales/items/'. $movimiento['id'], 'Ver detalles') }}
                        @elseif($movimiento['tipo'] == 'daño')
                            {{ HTML::link('damages/items/'. $movimiento['id'], 'Ver detalles') }}
                        @else
                            {{ HTML::link('instants/items/'. $movimiento['id'], 'Ver detalles') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@stop