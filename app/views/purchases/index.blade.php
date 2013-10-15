@extends('layouts.master')

@section('head')
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
                $('.pagination').addClass('btn-toolbar');
                $('.pagination ul').addClass('btn-group');
                $('.pagination ul li').addClass('btn btn-default');

                $('.acordion').accordion({
                    heightStyle: "content"
                });
            }

        })(jQuery);
    </script>

@stop

@section('content')
    <h1>Informe de compras</h1>
    <div class="acordion">
        <h3>Filtro por estado</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-status')) }}
                <div class="input-group">
                    {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de compra</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-id')) }}
                <div class="input-group">
                    {{ Form::input('number', 'idPurchase', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de compra', 'placeholder' => 'Código de compra', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código o parte del nombre de un artículo</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-article')) }}
                <div class="input-group">
                    {{ Form::text('article', '', array('class' => 'form-control', 'title' => 'Código o parte del nombre del artículo', 'placeholder' => 'Código o parte del nombre del artículo.', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por rango de fechas</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-dates')) }}
                <div class="input-group">

                    <span class="input-group-addon">Fecha inicio:</span>
                    <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />

                    <span class="input-group-addon">Fecha fin:</span>
                    <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />

                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>

                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por artículo y rango de fechas</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-article-dates')) }}

                    {{ Form::text('article', '', array('class' => 'form-control', 'title' => 'Código o parte del nombre del artículo', 'placeholder' => 'Código o parte del nombre del artículo.', 'required')) }}

                <div class="input-group">

                    <span class="input-group-addon">Fecha inicio:</span>
                    <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />

                    <span class="input-group-addon">Fecha fin:</span>
                    <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />

                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>

                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por comentarios</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-comments')) }}
                <div class="input-group">
                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la compra', 'placeholder' => 'Parte del comentario de la compra.', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
    </div> <!-- /.acordion -->

    @if(isset($filterPurchase))
        <div class="alert alert-dismissable alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ $filterPurchase }}
        </div>
    @endif
    <p> &nbsp; </p>
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