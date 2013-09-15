@extends('layouts.master')

@section('content')

    <h1>Artículos</h1>

    @foreach($articles as $article)
        <div class="panel panel-primary">
          <div class="panel-heading">
                {{ $article->name }}
          </div>
          <div class="panel-body">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>
                        Código
                    </th>
                    <th>
                        Medida
                    </th>
                    <th>
                        Precio
                    </th>
                    <th>
                        IVA
                    </th>
                </tr>
                <tr>
                    <td>
                        {{ $article->id }}
                    </td>
                    <td>
                        {{ $article->unit }}
                    </td>
                    <td>
                        {{ $article->price }}
                    </td>
                    <td>
                        {{ $article->iva }}%
                    </td>
                </tr>
            </table>
          </div>
          <p>{{ $article->comments }}</p>
          <div class="panel-footer">
             {{ Form::open(array('url' => 'cart/add')) }}
                    {{ Form::text('id', $article->id, array('class' => 'hidden')) }}
                    {{ Form::input('number', 'cantidad', '1.00', array('class' => 'form-control', 'min' => '0.00', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Cantidad', 'required')) }}
                    {{ Form::submit('Añadir al carrito', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}

                @if(Auth::check() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))
                    {{ HTML::link('articles/edit/'. $article->id, 'Editar', array('class' => 'btn btn-warning')) }}
                @endif
          </div>
        </div>
    @endforeach

@stop