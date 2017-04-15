@extends('layouts.master')

@section('head')

    @if(Config::get('app.entorno') == 'local')
        {{ HTML::style('css/jquery-ui-smoothness.css') }}
        {{ HTML::script('js/jquery-ui.js') }}
    @else
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
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
                alert('Faltan campos de la compra por diligenciar.');
            }
        }
    </script>

@stop

@section('content')
    <h1>Informe de entregas inmediatas</h1>
    <button class="btn btn-info" id="btnFiltrar">
        <span class="glyphicon glyphicon-filter"></span>
        Filtros
    </button>
    <div class="acordion">
        <h3>Filtro por sucursal</h3>
        <div>
            {{ Form::open(array('url' => 'instants/filter-by-branch', 'id' => 'branchForm', 'method' => 'get')) }}
                <div class="input-group">

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
        <h3>Filtro por código de entrega inmediata</h3>
        <div>
            {{ Form::open(array('url' => 'instants/filter-by-id', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::input('number', 'idInstant', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de entrega inmediata', 'placeholder' => 'Código de entrega inmediata', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo</h3>
        <div>
            {{ Form::open(array('url' => 'instants/filter-by-article', 'method' => 'get')) }}
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
            {{ Form::open(array('url' => 'instants/filter-by-dates', 'method' => 'get')) }}
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
            {{ Form::open(array('url' => 'instants/filter-by-article-dates', 'method' => 'get')) }}

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
            {{ Form::open(array('url' => 'instants/filter-by-comments', 'method' => 'get')) }}
                <div class="input-group">
                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la entrega inmediata', 'placeholder' => 'Parte del comentario de la entrega inmediata.', 'required')) }}
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>
                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
        <h3>Filtro por código de artículo y comentarios de remisionero</h3>
        <div>
            {{ Form::open(array('url' => 'instants/filter-by-article-comments', 'method' => 'get')) }}
                <div class="input-group">

                    {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}

                    <span class="input-group-addon">Comentarios: </span>

                    {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Parte del comentario de la entrega inmediata', 'placeholder' => 'Parte del comentario de la entrega inmediata.', 'required')) }}

                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">Aplicar</button>
                    </span>

                </div><!-- /input-group -->
            {{ Form::close() }}
        </div>
    </div> <!-- /.acordion -->

    @if(isset($filterInstant))
        <div class="alert alert-dismissable alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ $filterInstant }}
        </div>
    @endif
    <p> &nbsp; </p>

    @foreach($instants as $instant)
        <div class="col-lg-6 articulo">

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

        </div>
    @endforeach

    @if(isset($input))
        {{$instants->appends(array_except($input, 'page'))->links()}}
    @else
        {{$instants->links()}}
    @endif

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