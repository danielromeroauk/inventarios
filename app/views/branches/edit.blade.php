@extends('layouts.master')

@section('content')

    {{ Form::open(array('url' => 'branches/update', 'class' => 'form-horizontal col-lg-4')) }}
        {{ Form::input('hidden', 'id', $branch->id) }}
        <table class="table table-bordered table-condensed">
            <thead>
                <th cols="2" class="success">
                    Editar sucursal
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ Form::text('name', $branch->name, array('class' => 'form-control', 'placeholder' => 'Nombre', 'title' => 'Nombre', 'data-content' => 'Nombre de la sucursal.', 'maxlength' => '255', 'required')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::textarea('comments', $branch->comments, array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de la sucursal...', 'maxlength' => '255')) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ Form::submit('Registrar', array('class' => 'btn btn-primary hidden', 'id' => 'registrar')) }}
                        {{ HTML::link('#myModal', 'Guardar', array('class' => 'btn btn-primary', 'data-toggle' => 'modal')) }}
                        {{ HTML::link('branches', 'Cancelar', array('class' => 'btn btn-danger cancelar')) }}
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
          ¿Está seguro que desea guardar los cambios de la sucursal <strong>{{ $branch->name }}</strong>?
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
          <a href="javascript:$('#registrar').click();" class="btn btn-primary">Guardar</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

@stop