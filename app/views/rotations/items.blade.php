@extends('layouts.master')

@section('head')

    @if(Config::get('app.entorno') == 'local')
        {{ HTML::style('css/jquery-ui-start.css') }}
        {{ HTML::script('js/jquery-ui.js') }}
    @else
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/start/jquery-ui.css" />
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    @endif

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
                $('#rotationStoreForm').submit();
            } else {
                alert('Faltan campos por diligenciar.');
            }
        }
    </script>

@stop

@section('content')

        <div class="panel panel-success">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-retweet"></span>
                Rotación {{ $rotation->id }}
                Creada por {{ $rotation->user->name }}
                el día {{ $rotation->created_at }}
            </div>
            <div class="panel-body">
                <p><strong>{{ strtoupper($rotation->status) }}</strong></p>
                <ul class="rotation">
                    <li><strong>Origen:</strong> {{ $rotation->branch_from()->first()->name }}</li>
                    <li><strong>Destino:</strong> {{ $rotation->branch_to()->first()->name }}</li>
                </ul>
                <p>Modificada el día {{ $rotation->updated_at }}</p>
                <p class="well">{{ $rotation->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cód. Artículo</th>
                        <th>Nombre del artículo</th>
                        <th>Cantidad</th>
                    </tr>
                    @foreach($ritems as $ritem)
                        <tr>
                            <td><a href="{{url('articles/search?filterBy=id&search='. $ritem->article->id)}}">
                                {{ $ritem->article->id }}
                                </a>
                            </td>
                            <td>{{ $ritem->article->name }}</td>
                            <td>{{ $ritem->amount .' '. $ritem->article->unit }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                @foreach($rotation->rotationStore as $rstore)
                    <p class="label label-info">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $rstore->created_at }} por {{ $rstore->user->name }}
                    </p>
                    <p class="alert alert-success">
                        {{ $rstore->comments_from }}
                        {{ $rstore->comments_to }}
                    </p>
                @endforeach

                @if( $rotation->status == 'pendiente en origen' && ( Auth::user()->permitido('bodeguero') && Auth::user()->roles()->first()->branch->id == $rotation->branch_from()->first()->id ) )

                    {{ Form::open(array('url' => 'rotations/rotation-store', 'id' => 'rotationStoreForm')) }}
                        {{ Form::input('hidden', 'rotation', $rotation->id) }}
                        {{ Form::input('hidden', 'branch_from', $rotation->branch_from()->first()->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'false', array('id' => 'notap')) }}
                        {{ Form::textarea('comments_from', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero en origen.', 'maxlength' => '255', 'required')) }}

                        <a href="#rotationStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Rotar desde origen
                        </a>

                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                @elseif( $rotation->status == 'pendiente en destino' && ( Auth::user()->permitido('bodeguero') && Auth::user()->roles()->first()->branch->id == $rotation->branch_to()->first()->id ) )

                    {{ Form::open(array('url' => 'rotations/rotation-store', 'id' => 'rotationStoreForm')) }}
                        {{ Form::input('hidden', 'rotation', $rotation->id) }}
                        {{ Form::input('hidden', 'branch_to', $rotation->branch_to()->first()->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'false', array('id' => 'notap')) }}
                        {{ Form::textarea('comments_to', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero en destino.', 'maxlength' => '255', 'required')) }}

                        <a href="#rotationStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar rotación
                        </a>

                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                @elseif( $rotation->status =='pendiente en origen' && ( Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador') ) )

                    {{ Form::open(array('url' => 'rotations/rotation-store', 'id' => 'rotationStoreForm')) }}
                        {{ Form::input('hidden', 'rotation', $rotation->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'false', array('id' => 'notap')) }}
                        {{ Form::input('hidden', 'branch_from', $rotation->branch_from()->first()->id) }}
                        {{ Form::textarea('comments_from', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero en origen.', 'maxlength' => '255', 'required')) }}

                        <a href="#rotationStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Rotar desde origen
                        </a>

                        {{ '<a href="'. url('rotations/cancel/'. $rotation->id) .'" class="btn btn-danger btn-sm">
                        <span class="glyphicon glyphicon-minus-sign"></span>
                        Cancelar remisión
                        </a>' }}

                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                @elseif( $rotation->status =='pendiente en destino' && ( Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador') ) )

                    {{ Form::open(array('url' => 'rotations/rotation-store', 'id' => 'rotationStoreForm')) }}
                        {{ Form::input('hidden', 'rotation', $rotation->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'false', array('id' => 'notap')) }}
                        {{ Form::input('hidden', 'branch_to', $rotation->branch_to()->first()->id) }}
                        {{ Form::textarea('comments_to', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero en destino.', 'maxlength' => '255', 'required')) }}

                        <a href="#rotationStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar rotación
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}


                @elseif( $rotation->status != 'finalizado' )

                    {{ Form::open(array('url' => 'rotations/rotation-store', 'id' => 'rotationStoreForm')) }}
                        {{ Form::input('hidden', 'rotation', $rotation->id) }}
                        {{ Form::input('hidden', 'notaparcial', 'true', array('id' => 'notap')) }}
                        {{ Form::textarea('comments_to', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios.', 'maxlength' => '255', 'required')) }}

                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                @endif

                @if( $rotation->status != 'finalizado' )

                    <span class="btn btn-success btn-sm" id="notaparcial">
                        <span class="glyphicon glyphicon-comment"></span>
                        Comentar
                    </span>

                @endif

            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

      <!-- Modal -->
      <div class="modal fade" id="rotationStoreModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Deseas rotar? Si haces clic en <strong>Sí</strong> el stock será modificado.
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validar();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop