@extends('layouts.master')

@if($user->roles()->first()->name == 'administrador')
    <?php die('Acceso restringido. <script type="text/javascript">window.location.replace("'. url('users/list') .'");</script>'); ?>
@endif

@section('content')

    {{ Form::open(array('url' => 'users/update', 'class' => 'form-horizontal col-lg-4')) }}
        {{ Form::input('hidden', 'id', $user->id) }}
        <table class="table table-bordered table-condensed">
            <thead>
                <th cols="2" class="success">
                    Editar usuario
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ Form::text('name', $user->name, array('class' => 'form-control', 'placeholder' => 'Nombre completo', 'title' => 'Nombre', 'data-content' => 'Nombre del usuario.', 'maxlength' => '255', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::email('email', $user->email, array('class' => 'form-control', 'placeholder' => 'Email', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('branch', 'Sucursal', array('class' => 'label label-default')) }}
                        <select name="branch" id="branch" class="form-control" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    @if($branch->id == $user->roles()->first()->branch->id)
                                        {{ 'selected' }}
                                    @endif
                                >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('rol', 'Rol', array('class' => 'label label-default')) }}
                        {{ Form::select('rol', array('vendedor' => 'Vendedor', 'remisionero' => 'Remisionero', 'auditor' => 'Auditor', 'bodeguero' => 'Bodeguero'), $user->roles()->first()->name, array('class' => 'form-control')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Nuevo password', 'required')) }}
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
                        {{ HTML::link('users/list', 'Cancelar', array('class' => 'btn btn-danger cancelar')) }}
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
          ¿Está seguro que desea modificar el usuario <strong>{{ $user->name }}</strong>?
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
        if($('input[name=password]').val() == $('input[name=password2]').val()) {
            $('#registrar').click();
        } else {
            alert('Los passwords no coinciden');
        }
    }
  </script>

@stop