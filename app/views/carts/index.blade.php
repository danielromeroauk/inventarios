@extends('layouts.master')

@section('content')

        @if(!Session::has('cart'))
            Carrito vacío.
            <script type="text/javascript">
                window.location.replace("<?php echo url('articles'); ?>");
            </script>
        @endif

    <table class="table table-stripped table-hover table-bordered">
        <thead>
            <tr>
                <th>
                    Cantidad
                </th>
                <th>
                    Artículo
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td>
                    {{ HTML::link('cart/clear', 'Vaciar carrito', array('class' => 'btn btn-danger')) }}
                </td>
                <td>
                    {{ HTML::link('cart/send', 'Enviar remisión', array('class' => 'btn btn-primary')) }}
                </td>
            </tr>
        </tfoot>
        <tbody>
            @foreach(Session::get('cart') as $item)
                <tr>
                    <td>
                        {{ $item[1] }}
                        {{ $item[0]->unit }}
                    </td>
                    <td>
                        {{ $item[0]->name }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop