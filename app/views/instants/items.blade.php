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
                $('#instantStoreForm').submit();
            } else {
                alert('Faltan campos por diligenciar.');
            }
        }
    </script>

@stop

@section('content')

        <div class="panel panel-info">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-flash"></span>
                Código de entrega inmediata: {{ $instant->id }}
            </div>
            <div class="panel-body">
                <ul class="instant">
                    <li><strong>Fecha de creación:</strong> {{ $instant->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $instant->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $instant->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $instant->updated_at }}</li>
                </ul>
                <p><strong>Comentarios del remisionero:</strong> {{ $instant->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cantidad</th>
                        <th>Artículo</th>
                    </tr>
                    @foreach($iitems as $iitem)
                        <tr>
                            <td>{{ $iitem->amount .' '. $iitem->article->unit }}</td>
                            <td>{{ $iitem->article->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                @if(isset($instant->InstantStore->comments))

                    <p class="label label-info">Finalizado por {{ $instant->InstantStore->user->name }}</p>
                    <p class="alert alert-success">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $instant->InstantStore->comments }}
                    </p>

                @elseif((Auth::user()->permitido('bodeguero') || Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador')) && $instant->status == 'pendiente')

                    {{ Form::open(array('url' => 'instants/instant-store', 'id' => 'instantStoreForm')) }}
                        {{ Form::input('hidden', 'instant', $instant->id) }}
                        {{ Form::input('hidden', 'branch_id', $instant->branch->id) }}
                        {{ Form::textarea('comments', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero.', 'maxlength' => '255', 'required')) }}
                         <a href="#instantStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar entrega inmediata
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                    @if(Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador'))

                            {{ '<a href="'. url('instants/cancel/'. $instant->id) .'" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-minus-sign"></span>
                                Cancelar remisión
                            </a>' }}

                    @endif

                @endif
            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

      <!-- Modal -->
      <div class="modal fade" id="instantStoreModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Deseas finalizar la entrega inmediata? Si haces clic en <strong>Sí</strong> el stock disminuirá.
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validar();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop