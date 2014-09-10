@extends('layouts.master')

@section('content')
    <h1>Informe de compras</h1>

    @include('plugins.filtros')

    @foreach($purchases as $purchase)

        <div class="col-sm-6 remision">

            @if($purchase->status == 'pendiente')
                <div class="panel panel-danger">

            @elseif($purchase->status == 'finalizado')
                <div class="panel panel-success">

            @else
                <div class="panel panel-default">

            @endif

                <div class="panel-heading">
                    <span class="glyphicon glyphicon-leaf"></span>
                    Compra {{ $purchase->id }}
                    creada por {{ $purchase->user->name }}
                    el día {{ $purchase->created_at }}
                </div>

                <div class="panel-body">
                    <p><strong>{{ strtoupper( $purchase->status ) }}</strong> en {{ $purchase->branch->name }}.
                    <p>{{ $purchase->comments }}</p>
                </div>

                <div class="panel-footer">
                    {{ '<a href="'. url('purchases/items') .'/'. $purchase->id .'" class="btn btn-info btn-sm">
                        <span class="glyphicon glyphicon-search"></span>
                        Ver ítems y comentarios
                    </a>' }}
                </div>

            </div>

        </div>
    @endforeach

    @if(isset($input))
        {{ $purchases->appends(array_except($input, 'page'))->links() }}
    @else
        {{ $purchases->links() }}
    @endif

@stop