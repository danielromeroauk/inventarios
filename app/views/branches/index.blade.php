@extends('layouts.master')

@section('content')

    <div class="row">
        <h1>Sucursales</h1>
        <table class="table table-stripped table-hover table-bordered">
            <thead>
                <th>Código</th>
                <th>Nombre</th>
                <th>Comentarios</th>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                    <tr>
                        <td>
                            {{ $branch->id }}
                        </td>
                        <td>
                            {{ $branch->name }}
                        </td>
                        <td>
                            {{ $branch->comments }}
                        </td>
                        <td>
                            {{ HTML::link('branches/edit/'. $branch->id, 'Editar') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop