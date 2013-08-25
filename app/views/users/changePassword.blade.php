@extends('layouts.master')

@section('content')

    {{ Form::open(array('url' => 'users/change-password', 'class' => 'form-horizontal col-lg-4')) }}
        <table class="table table-bordered table-condensed">
            <thead>
                <th cols="2" class="success">
                    Cambiar password {{ Auth::user()->name }}
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ Form::label('password') }}
                        {{ Form::password('password0', array('class' => 'form-control', 'placeholder' => 'Password actual', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::password('password1', array('class' => 'form-control', 'placeholder' => 'Nuevo password', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::password('password2', array('class' => 'form-control', 'placeholder' => 'Repetir nuevo password', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::submit('Registrar', array('class' => 'btn btn-primary hidden', 'id' => 'registrar')) }}
                        {{ HTML::link('#myModal', 'Guardar', array('class' => 'btn btn-primary', 'data-toggle' => 'modal')) }}
                        {{ HTML::link('/', 'Cancelar', array('class' => 'btn btn-danger cancelar')) }}
                    </td>
                </tr>
            </tbody>
        </table>
    {{ Form::close() }}

  <!-- Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Confirmar</h4>
        </div>
        <div class="modal-body">
          ¿Está seguro que desea cambiar su password?
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
          <a href="javascript:compararPasswords();" class="btn btn-primary">Guardar</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <script type="text/javascript">
    function compararPasswords() {
        if($('input[name=password1]').val() == $('input[name=password2]').val()) {
            $('#registrar').click();
        } else {
            alert('Los passwords no coinciden');
        }
    }
  </script>

@stop