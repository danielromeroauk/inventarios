@extends('layouts.master')

@section('content')

    <h1>Informe de ventas</h1>

    @include('plugins.filtros')

    <table class="table table-hover table-bordered datos">
        <thead>
            <th>Código</th>
            <th>Sucursal</th>
            <th>Fecha</th>
            <th class="right">Cantidad</th>
            <th>Estado</th>
            <th>Comentario del remisionero</th>
            <th>Nota reciente</th>
            <th>Acción</th>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                @if($sale->status == 'cancelado')
                    <tr class="warning">

                @elseif($sale->status == 'pendiente')
                    <tr class="danger">

                @elseif($sale->status == 'finalizado')
                    <tr class="default">
                @else
                    <tr>
                @endif
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->branch()->first()->name }}</td>
                    <td>{{ $sale->created_at }}</td>
                    <td class="right">{{ $amounts[$sale->id] }}</td>
                    <td>{{ $sale->status }}</td>
                    <td>{{ $sale->comments }}</td>
                    <td>
                        @if(isset($sale->saleStore()->orderBy('created_at', 'desc')->first()->comments))
                            {{ $sale->saleStore()->orderBy('created_at', 'desc')->first()->comments }}
                        @endif
                    </td>
                    <td>
                        <span class="glyphicon glyphicon-search"></span>
                        {{ HTML::link('sales/items/'. $sale->id, 'Ver detalles') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(isset($input))
        {{$sales->appends(array_except($input, 'page'))->links()}}
    @else
         {{$sales->links()}}
    @endif

@stop