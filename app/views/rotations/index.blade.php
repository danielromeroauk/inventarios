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

    @foreach($rotations as $rotation)
        <div class="panel panel-success">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-retweet"></span>
                Código de rotación: {{ $rotation->id }}
            </div>
            <div class="panel-body">
                <ul class="rotation">
                    <li><strong>Estado:</strong> {{ $rotation->status }}</li>
                    <li><strong>Fecha de creación:</strong> {{ $rotation->created_at }}</li>
                    <li><strong>De la sucursal:</strong> {{ $rotation->branch_from()->first()->name }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $rotation->branch_to()->first()->name }}</li>
                    <li><strong>Usuario:</strong> {{ $rotation->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $rotation->updated_at }}</li>
                </ul>
                <p>{{ $rotation->comments }}</p>
            </div>
            <div class="panel-footer">
                {{ '<a href="'. url('rotations/items') .'/'. $rotation->id .'" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-list"></span>
                    Ver más detalles
                </a>' }}
            </div>
        </div>
    @endforeach

    <?php echo $rotations->links(); ?>

@stop