@extends('layouts.master')

@section('head')

    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
                $('.pagination').addClass('btn-toolbar');
                $('.pagination ul').addClass('btn-group');
                $('.pagination ul li').addClass('btn btn-default');
            }

        })(jQuery);
    </script>

@stop

@section('content')

    @foreach($sales as $sale)
        <div class="panel panel-info">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-leaf"></span>
                Código de venta: {{ $sale->id }}
            </div>
            <div class="panel-body">
                <ul class="sale">
                    <li><strong>Estado:</strong> {{ $sale->status }}</li>
                    <li><strong>Fecha de creación:</strong> {{ $sale->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $sale->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $sale->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $sale->updated_at }}</li>
                </ul>
                <p>{{ $sale->comments }}</p>
            </div>
            <div class="panel-footer">
                {{ '<a href="'. url('sales/items') .'/'. $sale->id .'" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-list"></span>
                    Ver más detalles
                </a>' }}
            </div>
        </div>
    @endforeach

    <?php echo $sales->links(); ?>

@stop