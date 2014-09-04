@extends('layouts.master')

@section('head')

    @if(Config::get('app.entorno') == 'local')
        {{ HTML::style('css/jquery-ui-smoothness.css') }}
        {{ HTML::script('js/jquery-ui.js') }}
    @else
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    @endif

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
                alert('Faltan campos de la venta por diligenciar.');
            }
        }
    </script>

@stop

@section('content')
    <h1>Informe de ventas</h1>
    <button class="btn btn-info" id="btnFiltrar">
        <span class="glyphicon glyphicon-filter"></span>
        Filtros
    </button>
    <div class="acordion">
        <h3>Filtro por estado, código de artículo y rango de fechas</h3>
        <div>
            {{ Form::open(array('url' => 'sales/filter-by-status-article-dates', 'method' => 'get')) }}

                <div class="input-group">

                    <span class="input-group-addon">Estado:</span>
                    {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}

                    <span class="input-group-addon">Artículo:</span>
                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

                </div>

                <br />

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
        <h3>Filtro por estado y sucursal</h3>
        <div>
            {{ Form::open(array('url' => 'sales/filter-by-status-branch', 'id' => 'branchForm', 'method' => 'get')) }}

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
            {{ Form::open(array('url' => 'sales/filter-by-status', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de venta</h3>
        <div>
            {{ Form::open(array('url' => 'sales/filter-by-id', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::input('number', 'idSale', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de venta', 'placeholder' => 'Código de venta', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo</h3>
        <div>
            {{ Form::open(array('url' => 'sales/filter-by-article', 'method' => 'get')) }}
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
            {{ Form::open(array('url' => 'sales/filter-by-dates', 'method' => 'get')) }}
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
            {{ Form::open(array('url' => 'sales/filter-by-article-dates', 'method' => 'get')) }}

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
            {{ Form::open(array('url' => 'sales/filter-by-comments', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la venta', 'placeholder' => 'Parte del comentario de la venta.', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo y comentarios de remisionero</h3>
        <div>
            {{ Form::open(array('url' => 'sales/filter-by-article-comments', 'method' => 'get')) }}
                <div class="input-group">

                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

                    <span class="input-group-addon">Comentarios: </span>

                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la venta', 'placeholder' => 'Parte del comentario de la venta.', 'required')) }}

                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>

                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
    </div> <!-- /.acordion -->

    @if(isset($filterSale))
        <div class="alert alert-dismissable alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ $filterSale }}
        </div>
    @endif
    <p> &nbsp; </p>

    @foreach($sales as $sale)
        <div class="col-lg-6 articulo">

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

        </div>
    @endforeach

    <?php
        if(isset($input)) {
            echo $sales->appends(array_except($input, 'page'))->links();
        } else {
            echo $sales->links();
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