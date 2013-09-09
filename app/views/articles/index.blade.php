@extends('layouts.master')

@section('content')

    <h1>Artículos</h1>

    @foreach($articles as $article)
        <ul class="table-bordered">
            <li>
                <strong>Código:</strong> {{ $article->id }}
            </li>
            <li>
                <strong>Nombre:</strong> {{ $article->name }}
            </li>
            <li>
                <strong>Unidad de medida:</strong> {{ $article->unit }}
            </li>
            <li>
                <strong>Precio:</strong> {{ $article->price }}
            </li>
            <li>
                <strong>IVA:</strong> {{ $article->iva }}%
            </li>
            <li>
                <strong>Comentarios:</strong> {{ $article->comments }}
            </li>
            <li>
                {{ Form::open(array('url' => 'cart/add')) }}
                    {{ Form::text('id', $article->id, array('class' => 'hidden')) }}
                    {{ Form::input('number', 'cantidad', '1.00', array('class' => 'form-control', 'style' => 'width:118px;display:inline-block;', 'min' => '0.00', 'step' => '0.01', 'max' => '99999999999999.99', 'title' => 'Cantidad', 'required')) }}
                    {{ Form::submit('Añadir al carrito', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}
            </li>
            <li>
                @if(Auth::check() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))
                    {{ HTML::link('articles/edit/'. $article->id, 'Editar', array('class' => 'btn btn-warning')) }}
                @endif
            </li>
        </ul>
        <hr />
    @endforeach

@stop