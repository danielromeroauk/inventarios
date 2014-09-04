@extends('layouts.master')

@section('head')
    <script>
        (function($){

            $(document).on('ready', function(){

                $('.pagination').addClass('btn-toolbar');
                $('.pagination ul').addClass('btn-group');
                $('.pagination ul li').addClass('btn btn-default');

            });

        })(jQuery);
    </script>

@stop

@section('content')

    <h1>Informe de ventas</h1>

    @if(isset($filtro))
        @if($filtro == 'articulo-fechas')
            @include('sales.filtro_articulo_fechas')
        @elseif($filtro == 'estado-articulo-fechas')
            @include('sales.filtro_estado_articulo_fechas')
        @endif
    @endif

    @if(isset($filterSale))
        <div class="alert alert-dismissable alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ $filterSale }}
        </div>
    @endif

    <table class="table table-striped table-hover table-bordered datos">
        <thead>
            <th>Código</th>
            <th>Sucursal</th>
            <th>Fecha</th>
            <th class="right">Cantidad</th>
            <th>Comentario del remisionero</th>
            <th>Notas recientes</th>
            <th>Acción</th>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->branch()->first()->name }}</td>
                    <td>{{ $sale->created_at }}</td>
                    <td class="right">{{ $amounts[$sale->id] }}</td>
                    <td>{{ $sale->comments }}</td>
                    <td>{{ $sale->saleStores()->first()->comments->last() }}</td>
                    <td>
                        <span class="glyphicon glyphicon-search"></span>
                        {{ HTML::link('sales/items/'. $sale->id, 'Ver detalles') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <?php
        if(isset($input)) {
            echo $sales->appends(array_except($input, 'page'))->links();
        } else {
            echo $sales->links();
        }
    ?>

@stop