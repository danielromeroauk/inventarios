@extends('layouts.master')

@section('head')

	@if(Config::get('app.entorno') == 'local')
		{{ HTML::style('css/jquery-ui-cupertino.css') }}
		{{ HTML::script('js/jquery-ui.js') }}
	@else
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/cupertino/jquery-ui.css" />
		<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	@endif

	<script>
		(function($){

			$(document).on('ready', iniciar);

			function iniciar() {
				convertirBotones();
				cargarBranches();

				$("#cargar_archivo").on('click', function(){
					$('#cargarModal .modal-body').load("{{ url('cart/desde-archivo') }}");
				});

			} //iniciar

		})(jQuery);

		function convertirBotones()
		{
			var contexto = $('#tabs');
			contexto.tabs();
			$('.button', contexto).button();
		}

		function cargarBranches()
		{
			$('#examinar1').on('click', function(){
				$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch&campo2=branch_id') }}" );
			});

			$('#examinar2').on('click', function(){
				$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch2&campo2=branch_id2') }}" );
			});

			$('#examinar3').on('click', function(){
				$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch3&campo2=branch_id3') }}" );
			});

			$('#examinar4').on('click', function(){
				$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch_from_name&campo2=branch_from') }}" );
			});

			$('#examinar5').on('click', function(){
				$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch_to_name&campo2=branch_to') }}" );
			});

		} // cargarBranches1

		function validarPurchase()
		{
			if ($('#branch_id').val() != '') {
				$('#purchaseForm').submit();
			} else {
				alert('Faltan campos de la compra por diligenciar.');
			}
		}

		function validarSale()
		{
			if ($('#branch_id2').val() != '')
			{
				var documento = $('#documento').val();
				var nombre = $('#nombre').val();
				var nit = $('#nit').val();
				var direccion = $('#direccion').val();

				if (documento != '')
				{
					if (nombre != '')
					{
						if (nit != '')
						{
							if (direccion != '')
							{
								$('#saleForm').submit();

							}
							else
							{
								alert('El campo direccion es obligatorio');
							}
						}
						else
						{
							alert('El campo NIT/CC es obligatorio');
						}
					}
					else
					{
						alert('El campo nombre es obligatorio');
					}
				}
				else
				{
					alert('El campo documento es obligatorio');
				}
			}
			else
			{
				alert('El campo sucursal es obligatorio');
			}
		}

		function validarDamage()
		{
			if ($('#branch_id3').val() != '') {
				$('#damageForm').submit();
			} else {
				alert('Faltan campos de daño por diligenciar.');
			}
		}

		function validarInstant()
		{
			if ($('#instant-comments').val() != '') {
				$('#instantForm').submit();
			} else {
				alert('Faltan campos de la entrega inmediata por diligenciar.');
			}
		}

		function validarRotation()
		{
			if ($('#rotation-comments').val() != '' && $('#branch_from').val() != '' && $('#branch_to').val() != '') {
				$('#rotationForm').submit();
			} else {
				alert('Faltan campos de la rotación por diligenciar.');
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
						<th>Cód. Artículo</th>
						<th>Nombre del artículo</th>
						<th>Cantidad</th>
						<th>
							{{ '<a href="'. url('cart/clear') .'" class="btn btn-danger btn-xs">
								<span class="glyphicon glyphicon-floppy-remove"></span>
								Vaciar carrito
							</a>' }}

							{{ '<a href="#cargarModal" class="btn btn-success btn-xs" id="cargar_archivo"  data-toggle="modal">
								<span class="glyphicon glyphicon-book"></span>
								Cargar desde Excel
							</a>' }}
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach(Session::get('cart') as $item)
						<tr>
							<td>
								<a href="{{ url( 'articles/search?filterBy=id&search='. $item['article']->id )}}">
									{{$item['article']->id}}
								</a>
							</td>
							<td>{{ $item['article']->name }}</td>
							<td>{{ number_format($item['amount'], 2, ',', '.') .' '. $item['article']->unit }}</td>
							<td>
								{{ '<a href="'. url('cart/clear-item/'. $item['article']->id) .'">
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

					@if(Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))

						<li><a href="#tab1"><span>Compra</span></a></li>
						<li><a href="#tab2"><span>Venta</span></a></li>
						<li><a href="#tab4"><span>Entrega inmediata</span></a></li>
						<li><a href="#tab5"><span>Rotación</span></a></li>

					@endif

					<li><a href="#tab3"><span>Daño</span></a></li>

				</ul>

				@if(Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))
					<div id="tab1">

						{{ Form::open(array('url' => 'purchases/add', 'id' => 'purchaseForm')) }}
								<div class="input-group">
									<span class="input-group-addon">Sucursal: </span>
									{{ Form::text('branch', '', array('id' => 'branch', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal que debe recibir el contenido del carrito.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
									<a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar1" data-toggle="modal">Examinar</a>
								</div> <!-- /input-group -->
								{{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id')) }}
								{{ Form::textarea('comments', '', array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de la compra.', 'maxlength' => '255')) }}
								<p> </p>

								<a href="#purchaseModal" class="button" data-toggle="modal">
									<span class="glyphicon glyphicon-floppy-save"></span>
									Enviar compra
								</a>
								{{ Form::submit('Enviar', array('class' => 'hidden')) }}
						{{ Form::close() }}

					</div><!-- /#tab1 -->

					<div id="tab2">

						{{ Form::open(array('url' => 'sales/add', 'id' => 'saleForm')) }}
								<div class="input-group">
									<span class="input-group-addon">Sucursal: </span>
									{{ Form::text('branch', '', array('id' => 'branch2', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal que debe entregar el contenido del carrito.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
									<a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar2" data-toggle="modal">Examinar</a>
								</div> <!-- /input-group -->

								{{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id2')) }}

								<div class="input-group">
									<span class="input-group-addon">Fecha real: </span>
									{{Form::input('date', 'fechareal', date('Y-m-d'), ['class' => 'form-control'])}}
								</div>

								{{Form::text('documento', 'FACTURA: ', ['id' => 'documento', 'class' => 'form-control', 'maxlength' => '20', 'required'])}}

								<div class="input-group">
									<span class="input-group-addon">Nombre: </span>
									{{Form::text('nombre', '', ['id' => 'nombre', 'class' => 'form-control', 'maxlength' => '130', 'required'])}}
								</div>

								{{Form::text('nit', 'NIT: ', ['id' => 'nit', 'class' => 'form-control', 'maxlength' => '20', 'required'])}}

								{{Form::textarea('direccion', 'DIRECCION: ', ['id' => 'direccion', 'rows' => '3', 'class' => 'form-control', 'maxlength' => '70', 'required'])}}

								<p> </p>

								<a href="#saleModal" class="button" data-toggle="modal">
									<span class="glyphicon glyphicon-floppy-save"></span>
									Enviar venta
								</a>

								{{ Form::submit('Enviar', array('class' => 'hidden')) }}

						{{ Form::close() }}

					</div><!-- /#tab2 -->

					<div id="tab4">

						{{ Form::open(array('url' => 'instants/add', 'id' => 'instantForm')) }}
								{{ Form::textarea('comments', '', array('id' => 'instant-comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de la entrega inmediata.', 'maxlength' => '255')) }}
								<p> </p>

								<a href="#instantModal" class="button" data-toggle="modal">
									<span class="glyphicon glyphicon-floppy-save"></span>
									Registrar entrega inmediata
								</a>
								{{ Form::submit('Enviar', array('class' => 'hidden')) }}
						{{ Form::close() }}

					</div><!-- /#tab4 -->

					<div id="tab5">

						{{ Form::open(array('url' => 'rotations/add', 'id' => 'rotationForm')) }}
								<div class="input-group">
									<span class="input-group-addon">Sucursal origen: </span>
									{{ Form::text('branch_from_name', '', array('id' => 'branch_from_name', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal origen.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
									<a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar4" data-toggle="modal">Examinar</a>
								</div> <!-- /input-group -->
								{{ Form::input('hidden', 'branch_from', '', array('id' => 'branch_from')) }}

								<div class="input-group">
									<span class="input-group-addon">Sucursal destino: </span>
									{{ Form::text('branch_to_name', '', array('id' => 'branch_to_name', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal destino.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
									<a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar5" data-toggle="modal">Examinar</a>
								</div> <!-- /input-group -->
								{{ Form::input('hidden', 'branch_to', '', array('id' => 'branch_to')) }}

								{{ Form::textarea('comments', '', array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de la rotación.', 'maxlength' => '255')) }}
								<p> </p>

								<a href="#rotationModal" class="button" data-toggle="modal">
									<span class="glyphicon glyphicon-floppy-save"></span>
									Enviar rotación
								</a>
								{{ Form::submit('Enviar', array('class' => 'hidden')) }}
						{{ Form::close() }}

					</div><!-- /#tab5 -->

				@endif

				<div id="tab3">

					{{ Form::open(array('url' => 'damages/add', 'id' => 'damageForm')) }}
							<div class="input-group">
								<span class="input-group-addon">Sucursal: </span>
								{{ Form::text('branch', '', array('id' => 'branch3', 'class' => 'form-control', 'placeholder' => 'Especifique la sucursal donde sucedió el daño.', 'title' => 'Sucursal', 'maxlength' => '255', 'required', 'readonly')) }}
								<a href="#branchesModal" class="input-group-addon btn btn-success" id="examinar3" data-toggle="modal">Examinar</a>
							</div> <!-- /input-group -->
							{{ Form::input('hidden', 'branch_id', '', array('id' => 'branch_id3')) }}
							{{ Form::textarea('comments', '', array('rows' => '3', 'class' => 'form-control', 'placeholder' => 'Datos adicionales de daño.', 'maxlength' => '255')) }}
							<p> </p>

							<a href="#damageModal" class="button" data-toggle="modal">
								<span class="glyphicon glyphicon-floppy-save"></span>
								Enviar daño
							</a>
							{{ Form::submit('Enviar', array('class' => 'hidden')) }}
					{{ Form::close() }}

				</div><!-- /#tab3 -->

			</div><!-- /#tabs -->

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
					¿Deseas enviar la remisión con el contenido actual del carrito?
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
					<a href="javascript:validarPurchase();" class="btn btn-primary">Sí</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Modal -->
	<div class="modal fade" id="saleModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
					<h4 class="modal-title">Confirmar</h4>
				</div>
				<div class="modal-body">
					¿Deseas enviar la venta con el contenido actual del carrito?
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
					<a href="javascript:validarSale();" class="btn btn-primary">Sí</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Modal -->
	<div class="modal fade" id="damageModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
					<h4 class="modal-title">Confirmar</h4>
				</div>
				<div class="modal-body">
					¿Deseas enviar el daño con el contenido actual del carrito?
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
					<a href="javascript:validarDamage();" class="btn btn-primary">Sí</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Modal -->
	<div class="modal fade" id="instantModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
					<h4 class="modal-title">Confirmar</h4>
				</div>
				<div class="modal-body">
					¿Deseas registrar la entrega inmediata con el contenido actual del carrito? Si haces clic en <strong>Sí</strong> el stock de la sucursal a la que estás asignado disminuirá inmediatamente.
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
					<a href="javascript:validarInstant();" class="btn btn-primary">Sí</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Modal -->
	<div class="modal fade" id="rotationModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
					<h4 class="modal-title">Confirmar</h4>
				</div>
				<div class="modal-body">
					¿Deseas registrar la rotación con el contenido actual del carrito?
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
					<a href="javascript:validarRotation();" class="btn btn-primary">Sí</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Modal -->
	<div class="modal fade" id="cargarModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
					<h4 class="modal-title">Cargar carrito desde excel</h4>
				</div>
				<div class="modal-body">
					No se ha podido cargar el formulario para cargar el archivo.
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

@stop