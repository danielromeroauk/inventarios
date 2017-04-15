<button class="btn btn-info" id="btnFiltrar">
    <span class="glyphicon glyphicon-filter"></span>
    Filtros
</button>
<div class="acordion">

    <h3>Filtro por comentario de remisionero</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-comments', 'method' => 'get')) }}
            <div class="input-group">
                {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Fragmento de comentario', 'placeholder' => 'Fragmento del comentario', 'required')) }}
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Aplicar</button>
                </span>
            </div><!-- /input-group -->
        {{ Form::close() }}
    </div>


    <h3>Filtro por estado, código de artículo y rango de fechas</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-status-article-dates', 'method' => 'get')) }}

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Estado:</span>
                {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Artículo:</span>
                {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Desde:</span>
                <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Hasta:</span>
                <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />
            </div>

            <div class="boton-aplicar">
                <button class="btn btn-primary" type="submit">Aplicar</button>
            </div>

        {{ Form::close() }}
    </div>


    <h3>Filtro por estado y sucursal</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-status-branch', 'id' => 'branchForm', 'method' => 'get')) }}

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Estado:</span>
                {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}
            </div>

            <div class="input-group col-sm-6">

                <span class="input-group-addon">Sucursal:</span>

                {{ Form::text('branch', '', array('id' => 'branch', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}

                <span class="input-group-btn">
                    <a href="#branchesModal" class="btn btn-default" id="examinar1" data-toggle="modal">Examinar</a>
                </span>

                {{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id')) }}

            </div><!-- /input-group -->

            <div class="boton-aplicar">
                <button class="btn btn-primary hidden" type="submit">Enviar</button>
                <input onClick="javascript:validarBranch();" class="btn btn-primary" type="button" value="Aplicar" />
            </div>

        {{ Form::close() }}
    </div>


    <h3>Filtro por estado</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-status', 'method' => 'get')) }}
            <div class="input-group">
                {{ Form::select('estado', array('pendiente' => 'Pendiente', 'cancelado' => 'Cancelado', 'finalizado' => 'Finalizado'), '', array('class' => 'form-control')) }}
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Aplicar</button>
                </span>
            </div><!-- /input-group -->
        {{ Form::close() }}
    </div>


    <h3>Filtro por código de remisión</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-id', 'method' => 'get')) }}
            <div class="input-group">
                {{ Form::input('number', 'idRemision', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de remisión', 'placeholder' => 'Código de remisión', 'required')) }}
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Aplicar</button>
                </span>
            </div><!-- /input-group -->
        {{ Form::close() }}
    </div>


    <h3>Filtro por código de artículo</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-article', 'method' => 'get')) }}
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
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-dates', 'method' => 'get')) }}

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Desde:</span>
                <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Hasta:</span>
                <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />
            </div>

            <div class="boton-aplicar">
                <button class="btn btn-primary" type="submit">Aplicar</button>
            </div>

        {{ Form::close() }}
    </div>


    <h3>Filtro por código de artículo y rango de fechas sin tener en cuenta las remisiones canceladas</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-article-dates', 'method' => 'get')) }}

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Artículo:</span>
                {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Desde:</span>
                <input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />
            </div>

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Hasta:</span>
                <input type="date" name="fecha2" class="form-control", title="Fecha fin" required />
            </div>

            <div class="boton-aplicar">
                <button class="btn btn-primary" type="submit">Aplicar</button>
            </div>

        {{ Form::close() }}
    </div>


    <h3>Filtro por código de artículo y comentario de remisionero</h3>
    <div>
        {{ Form::open(array('url' => $TIPO_REMISION .'/filter-by-article-comments', 'method' => 'get')) }}

            <div class="input-group col-sm-6">
                <span class="input-group-addon">Artículo: </span>

                {{ Form::input('number', 'article', '', array('class' => 'form-control', 'min' => '1', 'step' => '1', 'max' => '99999999999999.99', 'title' => 'Código de artículo', 'placeholder' => 'Código de artículo', 'required')) }}
            </div>

            <div class="input-group col-sm-6">

                <span class="input-group-addon">Comentario: </span>

                {{ Form::text('comments', '', array('class' => 'form-control', 'title' => 'Fragmento del comentario', 'placeholder' => 'Fragmento del comentario', 'required')) }}

            </div><!-- /input-group -->

            <div class="boton-aplicar">
                <button class="btn btn-primary" type="submit">Aplicar</button>
            </div>

        {{ Form::close() }}
    </div>
</div> <!-- /.acordion -->

@if(isset($mensaje))
    <div class="alert alert-dismissable alert-info">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      {{ $mensaje }}
    </div>
@endif

<p> </p>

@if(Config::get('app.entorno') == 'local')
    {{ HTML::style('css/jquery-ui-smoothness.css') }}
    {{ HTML::script('js/jquery-ui.js') }}
@else
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
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


<script>
    $(document).on('ready', function()
    {
        var acordion = $('.acordion');

        acordion.hide();

        $('.acordion').accordion({
            heightStyle: "content"
        });

        $("#btnFiltrar").on('click', function(){
            acordion.toggle("slow");
        });

        $('.pagination').addClass('btn-toolbar');
        $('.pagination ul').addClass('btn-group');
        $('.pagination ul li').addClass('btn btn-default');

        $('#examinar1').on('click', function(){
            $('#branchesModal .modal-body').load( "{{  url('branches/select?campo1=branch&campo2=branch_id') }}" );
        });

    });

    function validarBranch()
    {
        if ($('#branch_id').val() != '') {
            $('#branchForm').submit();
        } else {
            alert('Faltan campos de la remisión por diligenciar.');
        }
    }
</script>