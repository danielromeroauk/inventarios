<!-- <h2>Cargar carrito desde archivo</h2> -->

 {{ Form::open(array('url' => 'cart/desde-archivo', 'files' => true)) }}

    <div class="input-group">
        {{ Form::file('articulos', array('class' => 'form-control', 'required')) }}
        <span class="input-group-btn">
            {{ Form::submit('Cargar', array('class' => 'btn btn-primary')) }}
        </span>
    </div>

 {{ Form::close() }}