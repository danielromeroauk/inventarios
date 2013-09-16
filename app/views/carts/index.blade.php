@extends('layouts.master')

@section('content')

    @include('plugins.cart')
    <div class="panel panel-info">
        <div class="panel-heading">Contenido del carrito</div>
        <div class="panel-content">
            <table class="table table-stripped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>
                            Cantidad
                        </th>
                        <th>
                            Artículo
                        </th>
                        <th>
                            Acción
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(Session::get('cart') as $item)
                        <tr>
                            <td>
                                {{ $item[1] }}
                                {{ $item[0]->unit }}
                            </td>
                            <td>
                                {{ $item[0]->name }}
                            </td>
                            <td>
                                {{ '<a href="'. url('cart/clear-item/'. $item[0]->id) .'">
                                    <button class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-remove"></span>
                                        Quitar
                                    </button>
                                </a>' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /panel-content -->
        <div class="panel-footer">
            {{ '<a href="'. url('cart/clear') .'" class="btn btn-danger">
                <span class="glyphicon glyphicon-floppy-remove"></span>
                Vaciar carrito
            </a>' }}

            {{ '<a href="'. url('cart/send') .'" class="btn btn-primary">
                <span class="glyphicon glyphicon-floppy-save"></span>
                Enviar remisión
            </a>' }}
        </div>
</div><!-- /panel -->
@stop