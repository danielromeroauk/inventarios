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

    @foreach($purchases as $purchase)
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
            </div>
            <div class="panel-footer">
                {{ '<a href="'. url('purchases/items') .'/'. $purchase->id .'" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-list"></span>
                    Ver más detalles
                </a>' }}
            </div>
        </div>
    @endforeach

    <?php echo $purchases->links(); ?>

@stop