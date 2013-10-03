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

    @foreach($instants as $instant)
        <div class="panel panel-info">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-flash"></span>
                Código de la entrega inmediata: {{ $instant->id }}
            </div>
            <div class="panel-body">
                <ul class="instant">
                    <li><strong>Fecha de creación:</strong> {{ $instant->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $instant->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $instant->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $instant->updated_at }}</li>
                </ul>
                <p>{{ $instant->comments }}</p>
            </div>
            <div class="panel-footer">
                {{ '<a href="'. url('instants/items') .'/'. $instant->id .'" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-list"></span>
                    Ver más detalles
                </a>' }}
            </div>
        </div>
    @endforeach

    <?php echo $instants->links(); ?>

@stop