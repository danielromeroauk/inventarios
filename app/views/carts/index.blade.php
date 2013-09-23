@extends('layouts.master')

@section('head')

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/start/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
               convertirBotones();
               cargarBranches();
            } //iniciar

        })(jQuery);

        function convertirBotones()
        {
            var contexto = $('#tabs');
            contexto.tabs();
            $('.button', contexto).button();
        }

        function cargarBranches() {
            $('#examinar').on('click', function(){
                $('#branchesModal .modal-body').load( "{{  url('branches/select') }}" );
            });
        }

        function validarPurchase() {
            if ($('#branch_id').val() != '') {
                $('#purchaseForm').submit();
            } else {
                alert('Faltan campos por diligenciar');
            }
        }
    </script>

@stop

@section('content')
    @include('plugins.cart')

    <div class="panel panel-info">
        <div class="panel-heading"> Contenido del carrito </div>
        <div class="panel-content">
            <table class="table table-stripped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>
                            Cantidad
                        </th>
                        <th>
                            Artículo
                        </th>
                        <th>
                            {{ '<a href="'. url('cart/clear') .'" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-floppy-remove"></span>
                                Vaciar carrito
                            </a>' }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(Session::get('cart') as $item)
                        <tr>
                            <td>
                                {{ $item[1] }}
                                {{ $item[0]->unit }}
                            </td>
                            <td>
                                {{ $item[0]->name }}
                            </td>
                            <td>
                                {{ '<a href="'. url('cart/clear-item/'. $item[0]->id) .'">
                                    <button class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-remove"></span>
                                        Quitar
                                    </button>
                                </a>' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.panel-content -->
        <div class="panel-footer">

            <div id="tabs">
              <ul>
                <li><a href="#tab1"><span>Compra</span></a></li>
                <li><a href="#tab2"><span>Venta</span></a></li>
                <li><a href="#tab3"><span>Rotación</span></a></li>
              </ul>
              <div id="tab1">

                {{ Form::open(array('url' => 'purchases/add', 'id' => 'purchaseForm')) }}
                        <div class="input-group">
                            <span class="input-group-addon">Sucursal: </span>
                            {{ Form::text('branch', '', array('id' => 'branch', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal que debe recibir el contenido del carrito.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
                            <a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar" data-toggle="modal">Examinar</a>
                        </div> <!-- /input-group -->
                        {{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id')) }}
                        {{ Form::textarea('comments', '', array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de la compra...', 'maxlength' => '255')) }}
                        <p> </p>

                        <a href="#purchaseModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Enviar compra
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                {{ Form::close() }}

              </div><!-- /#tab1 -->
              <div id="tab2">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
              </div>
              <div id="tab3">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
              </div>
            </div>

        </div><!-- /.panel-footer -->
    </div><!-- /.panel -->

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

    <!-- Modal -->
      <div class="modal fade" id="purchaseModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Está seguro que desea enviar la remisión con el contenido actual del carrito?
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validarPurchase();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop