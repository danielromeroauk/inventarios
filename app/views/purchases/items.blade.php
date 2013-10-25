@extends('layouts.master')

@section('head')

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/start/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
               convertirBotones();
            } //iniciar

        })(jQuery);

        function convertirBotones()
        {
            $('.button').button();
        }

        function validar() {
            if ($('#comments').val() != '') {
                $('#purchaseStoreForm').submit();
            } else {
                alert('Faltan campos por diligenciar');
            }
        }
    </script>

@stop

@section('content')

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
                <p><strong>Comentarios del remisionero:</strong> {{ $purchase->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cantidad</th>
                        <th>Artículo</th>
                    </tr>
                    @foreach($pitems as $pitem)
                        <tr>
                            <td>{{ $pitem->amount .' '. $pitem->article->unit }}</td>
                            <td>{{ $pitem->article->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                @if(isset($purchase->PurchaseStore->comments))

                    <p class="label label-info">Finalizado por {{ $purchase->PurchaseStore->user->name }}</p>
                    <p class="alert alert-success">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $purchase->PurchaseStore->comments }}
                    </p>

                @elseif( ( (Auth::user()->permitido('bodeguero') && $purchase->branch->id == Auth::user()->roles()->first()->branch->id) || Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador') ) && $purchase->status == 'pendiente')

                    {{ Form::open(array('url' => 'purchases/purchase-store', 'id' => 'purchaseStoreForm')) }}
                        {{ Form::input('hidden', 'purchase', $purchase->id) }}
                        {{ Form::input('hidden', 'branch_id', $purchase->branch->id) }}
                        {{ Form::textarea('comments', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero.', 'maxlength' => '255', 'required')) }}
                         <a href="#purchaseStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar compra
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                    @if(Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador'))

                            {{ '<a href="'. url('purchases/cancel/'. $purchase->id) .'" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-minus-sign"></span>
                                Cancelar remisión
                            </a>' }}

                    @endif

                @endif
            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

      <!-- Modal -->
      <div class="modal fade" id="purchaseStoreModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Deseas finalizar la compra? Si haces clic en <strong>Sí</strong> el stock aumentará.
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validar();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop