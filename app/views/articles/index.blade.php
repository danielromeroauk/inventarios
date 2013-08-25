@extends('layouts.master')

@section('content')

    <div class="row">
        <h1>Artículos</h1>
        <table class="table table-stripped table-hover table-bordered">
            <thead>
                <th>Código</th>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Precio</th>
                <th>IVA</th>
                <th>Comentarios</th>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td>
                            {{ $article->id }}
                        </td>
                        <td>
                            {{ $article->name }}
                        </td>
                        <td>
                            {{ $article->unit }}
                        </td>
                        <td>
                            {{ $article->price }}
                        </td>
                        <td>
                            {{ $article->iva }}
                        </td>
                        <td>
                            {{ $article->comments }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop