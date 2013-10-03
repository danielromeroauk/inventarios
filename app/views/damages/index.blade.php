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

    @foreach($damages as $damage)
        <div class="panel panel-warning">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-flag"></span>
                Código de daño: {{ $damage->id }}
            </div>
            <div class="panel-body">
                <ul class="damage">
                    <li><strong>Estado:</strong> {{ $damage->status }}</li>
                    <li><strong>Fecha de creación:</strong> {{ $damage->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $damage->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $damage->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $damage->updated_at }}</li>
                </ul>
                <p>{{ $damage->comments }}</p>
            </div>
            <div class="panel-footer">
                {{ '<a href="'. url('damages/items') .'/'. $damage->id .'" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-list"></span>
                    Ver más detalles
                </a>' }}
            </div>
        </div>
    @endforeach

    <?php echo $damages->links(); ?>

@stop