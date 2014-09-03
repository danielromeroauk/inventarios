@extends('layouts.master')

@section('head')
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/start/jquery-ui.css" />
    {{-- HTML::style('css/jquery-ui-start.css') --}}

    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    {{-- HTML::script('js/jquery-ui.js') --}}

    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
               convertirBotones();

               $('#notaparcial').on('click', function(){
                    $('#notap').val(true);
                    validar();
                });

            } //iniciar

        })(jQuery);

        function convertirBotones()
        {
            $('.button').button();
        }

        function validar() {
            if ($('#comments').val() != '') {
                $('#saleStoreForm').submit();
            } else {
                alert('Faltan campos por diligenciar.');
            }
        }
    </script>

@stop

@section('content')

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
                <p><strong>Comentarios del remisionero:</strong> {{ $sale->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cód. Artículo</th>
                        <th>Nombre de artículo</th>
                        <th>Cantidad</th>
                    </tr>
                    @foreach($sitems as $sitem)
                        <tr>
                            <td>{{ $sitem->article->id }}</td>
                            <td>{{ $sitem->article->name }}</td>
                            <td>{{ $sitem->amount .' '. $sitem->article->unit }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                @foreach($sale->SaleStore as $sstore)
                    <p class="label label-info">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $sstore->created_at }} por {{ $sstore->user->name }}
                    </p>
                    <p class="alert alert-success">
                        {{ $sstore->comments }}
                    </p>
                @endforeach

                @if( ( (Auth::user()->permitido('bodeguero') && $sale->branch->id == Auth::user()->roles()->first()->branch->id) || Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador') ) && $sale->status == 'pendiente')

                    {{ Form::open(array('url' => 'sales/sale-store', 'id' => 'saleStoreForm')) }}
                        {{ Form::input('hidden', 'sale', $sale->id) }}
                        {{ Form::input('hidden', 'branch_id', $sale->branch->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'false', array('id' => 'notap')) }}
                        {{ Form::textarea('comments', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero.', 'maxlength' => '255', 'required')) }}
                        <span class="button" id="notaparcial">
                            <span class="glyphicon glyphicon-comment"></span>
                            Registrar nota parcial
                        </span>
                         <a href="#saleStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar venta
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                    @if(Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador'))

                            {{ '<a href="'. url('sales/cancel/'. $sale->id) .'" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-minus-sign"></span>
                                Cancelar remisión
                            </a>' }}

                    @endif

                @endif
            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

      <!-- Modal -->
      <div class="modal fade" id="saleStoreModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Deseas finalizar la venta? Si haces clic en <strong>Sí</strong> el stock disminuirá.
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validar();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop