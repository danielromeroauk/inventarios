@extends('layouts.master')

@section('content')
    <h1>Informe de ventas</h1>

    @include('plugins.filtros')

    @foreach($sales as $sale)

        <div class="col-sm-6 remision">

            @if($sale->status == 'pendiente')
                <div class="panel panel-danger">

            @elseif($sale->status == 'finalizado')
                <div class="panel panel-success">

            @else
                <div class="panel panel-default">

            @endif

                <div class="panel-heading">
                    <span class="glyphicon glyphicon-leaf"></span>
                    Venta {{ $sale->id }}
                    creada por {{ $sale->user->name }}
                    el día {{ $sale->created_at }}
                </div>

                <div class="panel-body">
                    <p><strong>{{ strtoupper( $sale->status ) }}</strong> en {{ $sale->branch->name }}.
                    <p>{{ $sale->comments }}</p>
                </div>

                <div class="panel-footer">
                    {{ '<a href="'. url('sales/items') .'/'. $sale->id .'" class="btn btn-info btn-sm">
                        <span class="glyphicon glyphicon-search"></span>
                        Ver ítems y comentarios
                    </a>' }}
                </div>

            </div>

        </div>
    @endforeach

    @if(isset($input))
        {{ $sales->appends(array_except($input, 'page'))->links() }}
    @else
        {{ $sales->links() }}
    @endif

@stop