@extends('layouts.master')

@section('content')

    <div class="row">
        <h1>Usuarios</h1>
        <table class="table table-stripped table-hover table-bordered">
            <thead>
                <th>Id</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            {{ $user->id }}
                        </td>
                        <td>
                            {{ $user->name }}
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            @foreach($user->roles as $rol)
                                {{ $rol->name }} en {{ $rol->branch->name }}
                            @endforeach
                        </td>
                        <td>
                            {{ HTML::link('users/edit/'. $user->id, 'Editar') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop