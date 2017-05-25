@extends('layouts.master')

@section('head')

	@if(Config::get('app.entorno') == 'local')
		{{ HTML::style('css/jquery-ui-smoothness.css') }}
		{{ HTML::script('js/jquery-ui.js') }}
	@else
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	@endif

	{{ HTML::script('js/jquery.timeago.js') }}

	<script>

		(function($){

			$(document).on('ready', iniciar);

			function iniciar() {
				$('.pagination').addClass('btn-toolbar');
				$('.pagination ul').addClass('btn-group');
				$('.pagination ul li').addClass('btn btn-default');

				$('.tabs').tabs({ active: 1 });

				$('.acordion').accordion({
					heightStyle: "content"
				});

				$('.auk-imagen').on('click', function(){
					var url = "{{ url('articles/image') }}" + "/" + $(this).attr('id') ;
					$('#imagenModal .modal-body').load( url );
				});

				$('#examinar1').on('click', function(){
					$('#branchesModal .modal-body').load( "{{ url('branches/select?campo1=branch&campo2=branch_id') }}" );
				});

				$("#btnFiltrar").on('click', function(){
					$(".acordion").toggle("slow");
				});

				$('.tabs ul li a').on('click', function()
					{
						var elem = $(this).attr('href') + ' .timeago';
						$(elem).timeago();
					});
			}

		})(jQuery);

	</script>

@stop

@section('content')

	<h1>Informe de Artículos</h1>

	{{ Form::open(array('url' => 'articles/search', 'method' => 'get', 'id' => 'busquedaform')) }}
		<div class="input-group col-xs-3 grupofilterBy">
			<span class="input-group-addon">
			<input type="radio" name="filterBy" value="id" /> Por código
			</span>
			<span class="input-group-addon">
			<input type="radio" name="filterBy" value="name" checked /> Por nombre
			</span>
			<span class="input-group-addon">
			<input type="radio" name="filterBy" value="comments" /> Por adicionales
			</span>
		</div>

		<div class="input-group grupobusqueda">
			{{ Form::text('search', '', array('placeholder' => 'Buscar...', 'class' => 'form-control', 'autofocus')) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Buscar</button>
				</span>
		</div><!-- /input-group -->
	{{ Form::close() }}

	@if(isset($filtro))
		<div class="alert alert-dismissable alert-info">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			{{ $filtro }}
		</div>
	@endif

	@foreach($articles as $article)
	<div class="col-lg-6 articulo">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<span class="glyphicon glyphicon-send"></span>
				{{ $article->name }}
			</div>

			<div class="panel-body">

			<div class="tabs">
				<ul>
					{{ '<li><a href="#tab1-'. $article->id .'"><span>Datos generales</span></a></li>' }}
					{{ '<li><a href="#tab2-'. $article->id .'"><span>Stock disponible</span></a></li>' }}
					{{ '<li><a href="#tab3-'. $article->id .'"><span>Stock en pendientes</span></a></li>' }}
				</ul>

				<div id="tab1-{{ $article->id }}">

					@if($article->ventaReciente() != '2013-01-01')
						<div class="alert alert-info">
							<p>
								La venta más reciente fue registrada
								<time class="timeago" datetime="{{ $article->ventaReciente() }}">{{ $article->ventaReciente() }}</time>.
							</p>

							<p>
								<a href="{{ url('sales/filter-by-article?article='. $article->id) }}" class="btn btn-default btn-xs">
									<span class="glyphicon glyphicon-search"></span>
									Ver más...
								</a>
							</p>
						</div>
					@endif

					<table class="table table-bordered table-hover">
						<tr>
							<th>Código</th>
							<th>Medida</th>
							@if(Auth::check() && !Auth::user()->permitido('vendedor'))
								<th>Costo</th>
							@endif
							<th>Precio</th>
							<th>IVA</th>
						</tr>
						<tr>
							<td>{{ $article->id }}</td>
							<td>{{ $article->unit }}</td>
							@if(Auth::check() && !Auth::user()->permitido('vendedor'))
								<td>{{ number_format($article->cost, 2, ',', '.') }}</td>
							@endif
							<td>{{ number_format($article->price, 2, ',', '.') }}</td>
							<td>{{ $article->iva }}%</td>
						</tr>
					</table>

					@if(!empty($article->comments))
						<p class="well">{{ $article->comments }}</p>
					@endif

					@if(Auth::check() && !Auth::user()->permitido('vendedor'))
						<a href="{{ url('articles/show-changes/'. $article->id) }}" class="link">
							<span class="glyphicon glyphicon-road"></span>
							Ver historial de cambios
						</a>
					@endif

				</div> <!-- /#tab1 -->

				<div id="tab2-{{ $article->id }}">
					<div class="article-image">
						@if(isset($article->image()->first()->image))
							{{ '<img src="'. url('img/articles/'. $article->image()->first()->image) .'" class="img-rounded">' }}
						@else
							<!-- <img src="http://placehold.it/150x150" /> -->
							<!-- <img src="{{ url('img/150x150.gif') }}" /> -->
							<div class="img"></div>
						@endif
					</div>

					<div style="display:inline-block;">

						<h3>$ {{ number_format($article->price, 2, ',', '.') }} COP</h3>

						@if(Auth::check() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero') || Auth::user()->permitido('bodeguero')))

							{{ Form::open(array('url' => 'cart/add', 'class' => 'form-inline')) }}

							{{ Form::text('id', $article->id, array('class' => 'hidden')) }}

							<div class="col-xs-9 alcarrito">
								{{ Form::input('number', 'cantidad', '1.00', array('class' => 'form-control input-sm', 'min' => '0.01', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Cantidad', 'required')) }}
							</div>

							<button type="submit" class="btn btn-success btn-sm">
								<span class="glyphicon glyphicon-shopping-cart"></span>
							</button>

							{{ Form::close() }}

						@endif

					</div>

					<div>
						<table class="table table-stripped table-hover">
							<tr>
								<th>Sucursal</th>
								<th>Stock disponible</th>
							</tr>
							@foreach($article->stocks as $stock)
								<tr>
									<td>{{ $stock->branch->name }}</td>
									<td>{{ number_format($article->disponible($stock->branch), 2, ',', '.') .' '. $article->unit }}</td>
								</tr>
							@endforeach
						</table>
					</div>

			  </div> <!-- /#tab2 -->

			  <div id="tab3-{{ $article->id }}">
				<div class="acordion">
					@foreach($branches as $branch)
						<h3>{{ $branch->name }}</h3>
						<div>
							<table class="table table-stripped table-hover">
								<tr>
									<th>Tipo de movimiento</th>
									<th>Cantidad pendiente</th>
								</tr>
								<tr>
									<td>Compra</td>
									<td>
										@if($article->inPurchases($branch) > 0)
											<a href="{{ url('purchases/filter-by-status-article-dates?estado=pendiente&article='. $article->id .'&fecha1=2000-01-01&fecha2=2038-12-31') }}" class="btn btn-default">
												{{ $article->inPurchases($branch) .' '. $article->unit }}
											</a>
										@else
											{{ $article->inPurchases($branch) .' '. $article->unit }}
										@endif
									</td>
								</tr>
								<tr>
									<td>Venta</td>
									<td>
										@if($article->inSales($branch) > 0)
											<a href="{{ url('sales/filter-by-status-article-dates?estado=pendiente&article='. $article->id .'&fecha1=2000-01-01&fecha2=2038-12-31') }}" class="btn btn-default">
												{{ $article->inSales($branch) .' '. $article->unit }}
											</a>
										@else
											{{ $article->inSales($branch) .' '. $article->unit }}
										@endif
									</td>
								</tr>
									<td>Origen de rotación</td>
									<td>
									    <a href="{{url('rotations/filter-by-status?estado=pendiente+en+origen')}}" class="btn btn-default">
									        {{ $article->inRotationsFrom($branch) .' '. $article->unit }}
									    </a>
									</td>
								</tr>
									<td>Destino de rotación</td>
									<td>
									    <a href="{{url('rotations/filter-by-status?estado=pendiente+en+destino')}}" class="btn btn-default">
									        {{ $article->inRotationsTo($branch) .' '. $article->unit }}
									    </a>
									</td>
								</tr>
									<td>Daño</td>
									<td>
										@if($article->inDamages($branch) > 0)
											<a href="{{ url('damages/filter-by-article-dates?article='. $article->id .'&fecha1=2000-01-01&fecha2=2038-12-31') }}" class="btn btn-default">
												{{ $article->inDamages($branch) .' '. $article->unit }}
											</a>
										@else
											{{ $article->inDamages($branch) .' '. $article->unit }}
										@endif
									</td>
								</tr>
							</table>
						</div>
					@endforeach

					<h3>Informe de todos los movimientos en cualquier estado</h3>
					<div>
						{{ Form::open(array('url' => 'articles/movimientos/'. $article->id, 'method' => 'get')) }}

							<div class="input-group">
								<span class="input-group-addon">Desde:</span>
								<input type="date" name="fecha1" class="form-control", title="Fecha inicio" required />
							</div>

							<div class="input-group">
								<span class="input-group-addon">Hasta:</span>
								<input type="date" name="fecha2" class="form-control", title="Fecha fin" required />
							</div>

							<br />

							<button class="btn btn-default btn-sm" type="submit">Generar informe</button>

						{{ Form::close() }}
					</div>

				</div> <!-- /.acordion -->
			  </div> <!-- /#tab3 -->

			</div> <!-- /#tabs -->

		  </div> <!-- /.panel-body -->

			@if(Auth::check() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))

			  <div class="panel-footer">

					{{ '<a href="'. url('articles/edit/'. $article->id) .'" class="btn btn-primary btn-sm">
						<span class="glyphicon glyphicon-edit"></span>
						Editar
					</a>' }}

					{{ '<a href="#imagenModal" data-toggle="modal" class="btn btn-info btn-sm auk-imagen" id="'. $article->id .'">
						<span class="glyphicon glyphicon-picture"></span>
						Cambiar imagen
					</a>' }}

					{{ '<a href="'. url('articles/excel-by-article/'. $article->id) .'" class="btn btn-success btn-sm auk-imagen" id="'. $article->id .'">
						<span class="glyphicon glyphicon-export"></span>
						Exportar stock
					</a>' }}

					@if(isset($article->image()->first()->image))
						{{ '<a href="'. url('articles/quitar-imagen/'. $article->id) .'" class="btn btn-danger btn-sm" id="'. $article->id .'">
							<span class="glyphicon remove"></span>
							Quitar imagen
						</a>' }}
					@endif

			  </div> <!-- /.panel.footer -->

			@endif

		</div> <!-- /.panel.panel-primary -->

	</div> <!-- /.col-lg-6 -->
	@endforeach

	@if(isset($input))
		{{$articles->appends(array_except($input, 'page'))->links()}}
	@else
		{{$articles->links()}}
	@endif

	<!-- Modal -->
	  <div class="modal fade" id="imagenModal">
		<div class="modal-dialog">
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
			  <h4 class="modal-title">Cambiar imagen</h4>
			</div>
			<div class="modal-body">
			  No se ha podido cargar el formulario para cambiar la imagen.
			</div>
		  </div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->

@stop