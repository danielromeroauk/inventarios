@extends('layouts.master')

@section('content')

	{{ Form::open(array('url' => 'articles/update', 'class' => 'form-horizontal col-lg-4')) }}
		{{ Form::input('hidden', 'id', $article->id) }}
		<table class="table table-bordered table-condensed">
			<thead>
				<th cols="2" class="success">
					Editar artículo
				</th>
			</thead>
			<tbody>
				<tr>
					<td>
						{{ Form::text('name', $article->name, array('class' => 'form-control', 'placeholder' => 'Artículo', 'title' => 'Artículo', 'data-content' => 'Nombre del artículo.', 'maxlength' => '255', 'required')) }}
					</td>
				</tr>
				<tr>
					<td>
						<div class="input-group">
							<span class="input-group-addon">Medida:</span>
						{{ Form::select('unit', Article::$medidas, $article->unit, array('class' => 'form-control', 'required')) }}
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="input-group">
							<span class="input-group-addon">Costo: $</span>
							{{ Form::input('number', 'cost', $article->cost, array('class' => 'form-control', 'min' => '0.01', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Costo', 'id' => 'cost', 'required')) }}
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="input-group">
							<span class="input-group-addon">Precio: $</span>
							{{ Form::input('number', 'price', $article->price, array('class' => 'form-control', 'min' => '0.01', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Precio', 'id' => 'price', 'required')) }}
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="input-group">
							<span class="input-group-addon">IVA:</span>
							{{ Form::select('iva', array('0' => '0', '5' => '5', '16' => '16'), $article->iva, array('class' => 'form-control', 'id' => 'iva', 'required')) }}
							<span class="input-group-addon">%</span>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						{{ Form::textarea('comments', $article->comments, array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales del artículo...', 'maxlength' => '255')) }}
					</td>
				</tr>
				<tr>
					<td>
						{{ Form::submit('Modificar', array('class' => 'btn btn-primary hidden', 'id' => 'modificar')) }}
						{{ HTML::link('#myModal', 'Modificar', array('class' => 'btn btn-primary', 'data-toggle' => 'modal')) }}
						{{ HTML::link('articles', 'Cancelar', array('class' => 'btn btn-danger cancelar')) }}
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
		  ¿Está seguro que desea modificar el artículo <strong>{{ $article->name }}</strong>?
		</div>
		<div class="modal-footer">
		  <a href="#" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
		  <a href="javascript:$('#modificar').click();" class="btn btn-primary">Guardar cambios</a>
		</div>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  {{ HTML::script('js/calculo-precio.js') }}

@stop