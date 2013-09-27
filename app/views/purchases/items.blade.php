@extends('layouts.master')

@section('content')

        <div class="panel panel-success">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-list-alt"></span>
                Código de compra: {{ $purchase->id }}
            </div>
            <div class="panel-body">
                <ul class="purchase">
                    <li><strong>Estado:</strong> {{ $purchase->status }}</li>
                    <li><strong>Fecha de creación:</strong> {{ $purchase->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $purchase->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $purchase->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $purchase->updated_at }}</li>
                </ul>
                <p>{{ $purchase->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cantidad</th>
                        <th>Artículo</th>
                    </tr>
                    @foreach($pitems as $pitem)
                        <tr>
                            <td>{{ $pitem->amount .' '. $pitem->article->unit }}</td>
                            <td>{{ $pitem->article->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                <p>Colocar aquí notas del bodeguero al recibir la remisión.</p>
            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

@stop