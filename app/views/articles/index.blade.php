@extends('layouts.master')

@section('head')
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
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
            }

        })(jQuery);
    </script>

@stop

@section('content')

    <h1>
        Informe de Artículos
        {{ Form::open(array('url' => 'articles/search')) }}
            <div class="input-group">
              {{ Form::text('search', '', array('placeholder' => 'Buscar...', 'class' => 'form-control')) }}
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Buscar</button>
              </span>
            </div><!-- /input-group -->
        {{ Form::close() }}
    </h1>

        @if(Session::has('filtro'))
            <div class="alert alert-dismissable alert-info">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              {{ Session::get('filtro') }}
            </div>
        @endif

    @foreach($articles as $article)

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
                {{ '<li><a href="#tab3-'. $article->id .'"><span>Stock en movimientos pendientes</span></a></li>' }}
              </ul>

              <div id="tab1-{{ $article->id }}">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Código</th>
                        <th>Medida</th>
                        <th>Costo</th>
                        <th>Precio</th>
                        <th>IVA</th>
                    </tr>
                    <tr>
                        <td>{{ $article->id }}</td>
                        <td>{{ $article->unit }}</td>
                        <td>{{ $article->cost }}</td>
                        <td>{{ $article->price }}</td>
                        <td>{{ $article->iva }}%</td>
                    </tr>
                </table>
                <p>{{ $article->comments }}</p>
              </div> <!-- /#tab1 -->

              <div id="tab2-{{ $article->id }}">
                <div class="article-image">
                    @if(isset($article->image()->first()->image))
                        {{ '<img src="img/articles/'. $article->image()->first()->image .'">' }}
                    @else
                        <img src="http://placehold.it/150x150" />
                    @endif
                </div>
                <div style="display:inline-block;">
                    <table class="table table-stripped table-hover">
                        <tr>
                            <th>Sucursal</th>
                            <th>Stock disponible</th>
                        </tr>
                        @foreach($article->stocks as $stock)
                            <tr>
                                <td>{{ $stock->branch->name }}</td>
                                <td>{{ $article->disponible($stock->branch) .' '. $article->unit }}</td>
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
                                    <td>{{ $article->inPurchases($branch) .' '. $article->unit }}</td>
                                </tr>
                                <tr>
                                    <td>Venta</td>
                                    <td>{{ $article->inSales($branch) .' '. $article->unit }}</td>
                                </tr>
                                    <td>Origen de rotación</td>
                                    <td>{{ $article->inRotationsFrom($branch) .' '. $article->unit }}</td>
                                </tr>
                                    <td>Destino de rotación</td>
                                    <td>{{ $article->inRotationsTo($branch) .' '. $article->unit }}</td>
                                </tr>
                                    <td>Daño</td>
                                    <td>{{ $article->inDamages($branch) .' '. $article->unit }}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div> <!-- /.acordion -->
              </div> <!-- /#tab3 -->

            </div> <!-- /#tabs -->

          </div> <!-- /.panel-body -->

          <div class="panel-footer">
             {{ Form::open(array('url' => 'cart/add')) }}
                    {{ Form::text('id', $article->id, array('class' => 'hidden')) }}
                    {{ Form::input('number', 'cantidad', '1.00', array('class' => 'form-control', 'min' => '0.01', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Cantidad', 'required')) }}
                    <button type="submit" class="btn btn-success btn-sm">
                        <span class="glyphicon glyphicon-shopping-cart"></span>
                        Al carrito
                    </button>
                {{ Form::close() }}

                @if(Auth::check() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))

                    {{ '<a href="'. url('articles/edit/'. $article->id) .'" class="btn btn-warning btn-sm">
                        <span class="glyphicon glyphicon-edit"></span>
                        Editar
                    </a>' }}

                    {{ '<a href="#imagenModal" data-toggle="modal" class="btn btn-danger btn-sm auk-imagen" id="'. $article->id .'">
                        <span class="glyphicon glyphicon-picture"></span>
                        Cambiar imagen
                    </a>' }}

                    {{ '<a href="'. url('articles/excel-by-article/'. $article->id) .'" class="btn btn-info btn-sm auk-imagen" id="'. $article->id .'">
                        <span class="glyphicon glyphicon-download-alt"></span>
                        Descargar stock
                    </a>' }}

                @endif

          </div> <!-- /.panel.footer -->
        </div> <!-- /.panel.panel-primary -->

    @endforeach

    <?php echo $articles->links(); ?>

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