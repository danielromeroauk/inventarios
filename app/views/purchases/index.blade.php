@extends('layouts.master')

@section('head')
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    {{-- HTML::style('css/jquery-ui-smoothness.css') --}}

    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    {{-- HTML::script('js/jquery-ui.js') --}}

    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
                $(".acordion").hide();

                $('.pagination').addClass('btn-toolbar');
                $('.pagination ul').addClass('btn-group');
                $('.pagination ul li').addClass('btn btn-default');

                $('.acordion').accordion({
                    heightStyle: "content"
                });

                $('#examinar1').on('click', function(){
                    $('#branchesModal .modal-body').load( "{{  url('branches/select?campo1=branch&campo2=branch_id') }}" );
                });

                $("#btnFiltrar").on('click', function(){
                    $(".acordion").toggle("slow");
                });

            }

        })(jQuery);

        function validarBranch()
        {
            if ($('#branch_id').val() != '') {
                $('#branchForm').submit();
            } else {
                alert('Faltan campos de la compra por diligenciar.');
            }
        }
    </script>

@stop

@section('content')
    <h1>Informe de compras</h1>
    <button class="btn btn-info" id="btnFiltrar">
        <span class="glyphicon glyphicon-filter"></span>
        Filtros
    </button>
    <div class="acordion">
        <h3>Filtro por estado y sucursal</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-status-branch', 'id' => 'branchForm', 'method' => 'get')) }}
                <div class="input-group">

                    <span class="input-group-addon">Estado:</span>
                    {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}

                    <span class="input-group-addon">Sucursal:</span>
                    {{ Form::text('branch', '', array('id' => 'branch', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
                            <a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar1" data-toggle="modal">Examinar</a>
                        {{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id')) }}

                    <span class="input-group-btn">
                        <button class="btn btn-primary hidden" type="submit">Enviar</button>
                        <input onClick="javascript:validarBranch();" class="btn btn-info" type="button" value="Aplicar" />
                    </span>

                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por estado</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-status', 'method' => 'get')) }}
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
            {{ Form::open(array('url' => 'purchases/filter-by-id', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::input('number', 'idPurchase', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de compra', 'placeholder' => 'Código de compra', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-article', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por rango de fechas</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-dates', 'method' => 'get')) }}
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
        <h3>Filtro por código de artículo y rango de fechas</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-article-dates', 'method' => 'get')) }}

                <div class="input-group">
                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

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
        <h3>Filtro por comentarios de remisionero</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-comments', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la compra', 'placeholder' => 'Parte del comentario de la compra.', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo y comentarios de remisionero</h3>
        <div>
            {{ Form::open(array('url' => 'purchases/filter-by-article-comments', 'method' => 'get')) }}
                <div class="input-group">

                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

                    <span class="input-group-addon">Comentarios: </span>

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
        <div class="col-lg-6 articulo">

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

        </div>
    @endforeach

    <?php
        if(isset($input)) {
            echo $purchases->appends(array_except($input, 'page'))->links();
        } else {
            echo $purchases->links();
        }
    ?>

    <!-- Modal -->
    <div class="modal fade" id="branchesModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Sucursales</h4>
            </div>
            <div class="modal-body">
              Las sucursales no han podido mostrarse.
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop