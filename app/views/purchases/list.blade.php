@extends('layouts.master')

@section('content')

    <h1>Informe de compras</h1>

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
            @foreach($purchases as $purchase)
                @if($purchase->status == 'cancelado')
                    <tr class="warning">

                @elseif($purchase->status == 'pendiente')
                    <tr class="danger">

                @elseif($purchase->status == 'finalizado')
                    <tr class="default">
                @else
                    <tr>
                @endif
                    <td>{{ $purchase->id }}</td>
                    <td>{{ $purchase->branch()->first()->name }}</td>
                    <td>{{ $purchase->created_at }}</td>
                    <td class="right">{{ $amounts[$purchase->id] }}</td>
                    <td>{{ $purchase->status }}</td>
                    <td>{{ $purchase->comments }}</td>
                    <td>
                        @if(isset($purchase->purchaseStore()->orderBy('created_at', 'desc')->first()->comments))
                            {{ $purchase->purchaseStore()->orderBy('created_at', 'desc')->first()->comments }}
                        @endif
                    </td>
                    <td>
                        <span class="glyphicon glyphicon-search"></span>
                        {{ HTML::link('purchases/items/'. $purchase->id, 'Ver detalles') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(isset($input))
        {{$purchases->appends(array_except($input, 'page'))->links()}}
    @else
        {{$purchases->links()}}
    @endif

@stop