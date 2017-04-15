@extends('layouts.master')

@section('head')

    @if(Config::get('app.entorno') == 'local')
        {{ HTML::script('js/jquery-ui.js') }}
    @else
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    @endif

    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
                $('.pagination').addClass('btn-toolbar');
                $('.pagination ul').addClass('btn-group');
                $('.pagination ul li').addClass('btn btn-default');
            }

        })(jQuery);
    </script>

@stop

@section('content')

    <h1>Artículo <strong>{{ $article[0]->article_id }}</strong> - <small>Historial de cambios</small></h1>

    @foreach($article as $change)

        <?php
            $log = json_decode($change->log);
            $user = User::find($change->user_id);
        ?>

        <div class="panel panel-info">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-bookmark"></span>
                {{ $user->name }}
                en {{ $change->created_at }}
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Nombre</th>
                        <th>Medida</th>
                        <th>Costo</th>
                        <th>Precio</th>
                        <th>IVA</th>
                    </tr>
                    <tr>
                        <td>{{ $log->name }}</td>
                        <td>{{ $log->unit }}</td>
                        <td>{{ number_format($log->cost, 2, ',', '.') }}</td>
                        <td>{{ number_format($log->price, 2, ',', '.') }}</td>
                        <td>{{ $log->iva }}%</td>
                    </tr>
                </table>
                <p>{{ $log->comments }}</p>
            </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->

    @endforeach

    {{$article->links()}}

@stop