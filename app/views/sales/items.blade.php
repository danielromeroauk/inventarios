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
        $(document).on('ready', function()
        {
            $('.button').button();
            $('#notaparcial').on('click', validar);

        });

        function validar()
        {
            if ($('#comments').val() != '')
            {
                $('#storeForm').submit();

            } else
            {
                alert('Faltan campos por diligenciar.');
            }
        }
    </script>

@stop

@section('content')

    @if($sale->status == 'pendiente')
        <div class="panel panel-danger">

    @elseif($sale->status == 'finalizado')
        <div class="panel panel-success">

    @else
        <div class="panel panel-default">

    @endif

        <div class="panel-heading">
            <span class="glyphicon glyphicon-leaf"></span>
            Venta {{ $sale->id }}
            creada por {{ $sale->user->name }}
            el día {{ $sale->created_at }}
        </div>

        <div class="panel-body">
            <p><strong>{{ strtoupper( $sale->status ) }}</strong> en {{ $sale->branch->name }}.</p>
            <p>{{ $sale->comments }}</p>

            <table class="table table-striped table-bordered">
                <tr>
                    <th>Cód. Artículo</th>
                    <th>Nombre de artículo</th>
                    <th>Cantidad</th>
                </tr>
                @foreach($sitems as $sitem)
                    <tr>
                        <td>
                            <a href="{{ url('articles/search?filterBy=id&search='. $sitem->article->id) }}">
                                {{ $sitem->article->id }}
                            </a>
                        </td>
                        <td>{{ $sitem->article->name }}</td>
                        <td>{{ $sitem->amount .' '. $sitem->article->unit }}</td>
                    </tr>
                @endforeach
            </table>

            @foreach($sale->saleStore as $sstore)

                <div class="col-md-6">

                    <div class="label label-primary">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $sstore->user->name }}
                    </div>
                    <div class="label label-success">
                         {{ $sstore->created_at }}
                    </div>
                    <p class="alert alert-info">
                        {{ $sstore->comments }}
                    </p>

                </div>

            @endforeach

        </div><!-- /.panel-body -->


        <div class="panel-footer">

            {{ Form::open(array('url' => 'sales/sale-store', 'id' => 'storeForm')) }}

                {{ Form::input('hidden', 'sale', $sale->id) }}
                {{ Form::input('hidden', 'branch_id', $sale->branch->id) }}

                {{ Form::textarea('comments', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios de bodeguero.', 'maxlength' => '255', 'required')) }}

                <button type="submit" class="btn btn-success btn-sm" id="notaparcial" name="notaparcial">
                    <span class="glyphicon glyphicon-comment"></span>
                    Comentar
                </button>

                @if( $sale->status == 'pendiente' &&
                    in_array(Auth::user()->roles()->first()->name, array('administrador', 'remisionero', 'bodeguero')) )

                     <a href="#modalConfirmar" class="btn btn-primary btn-sm" data-toggle="modal">
                        <span class="glyphicon glyphicon-floppy-save"></span>
                        Finalizar venta
                    </a>

                @endif

                {{ Form::submit('Enviar', array('class' => 'hidden')) }}

            {{ Form::close() }}

            @if(Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador'))

                @if($sale->status == 'pendiente')

                    {{ '<a href="'. url('sales/cancel/'. $sale->id) .'" class="btn btn-danger btn-sm">
                        <span class="glyphicon glyphicon-minus-sign"></span>
                        Cancelar remisión
                    </a>' }}

                @endif

            @endif

        </div><!-- /.panel-footer -->

    </div><!-- /.panel -->

  <!-- Modal -->
  <div class="modal fade" id="modalConfirmar">
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