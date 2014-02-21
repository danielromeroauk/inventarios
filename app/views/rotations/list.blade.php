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

    <h1>Informe de rotaciones</h1>

    <div>
        {{ Form::open(array('url' => 'purchases/filter-by-article-dates', 'method' => 'get')) }}

            <div class="input-group">
                {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

                <span class="input-group-addon">Fecha inicio:</span>
                <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />

                <span class="input-group-addon">Fecha fin:</span>
                <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />

                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </span>

            </div><!-- /input-group -->
        {{ Form::close() }}
    </div>

    @if(isset($filterRotation))
        <div class="alert alert-dismissable alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ $filterRotation }}
        </div>
    @endif

    <table class="table table-striped table-hover table-bordered datos">
        <thead>
            <th>Código</th>
            <th>Fecha</th>
            <th class="right">Cantidad</th>
            <th>Comentarios</th>
            <th>Acción</th>
        </thead>
        <tbody>
            @foreach($rotations as $rotation)
                <tr>
                    <td>{{ $rotation->id }}</td>
                    <td>{{ $rotation->created_at }}</td>
                    <td class="right">{{ $amounts[$rotation->id] }}</td>
                    <td>{{ $rotation->comments }}</td>
                    <td>
                        <span class="glyphicon glyphicon-search"></span>
                        {{ HTML::link('rotations/items/'. $rotation->id, 'Ver detalles') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <?php
        if(isset($input)) {
            echo $rotations->appends(array_except($input, 'page'))->links();
        } else {
            echo $rotations->links();
        }
    ?>

@stop