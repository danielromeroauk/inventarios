<p class="justify">Se requiere que las columnas <strong>A</strong> y <strong>B</strong> de la hoja de Excel correspondan al <strong>código de barras</strong> y <strong>cantidad</strong> respectivamente. El sistema tomará la hoja activa del documento, es decir, la que se estaba viendo la última vez que se guardó el archivo de Excel, por lo que se recomienda tener una sola hoja en el archivo de Excel.</p>

<p class="justify">Para que un artículo pueda ser cargado al carrito desde Excel, debe tener el código de barras registrado en <strong>datos adicionales</strong> dentro del sistema.</p>

<p class="justify">Los códigos de barras que no sean encontrados se mostrarán en un mensaje de alerta y no se agregarán al carrito.</p>

<p class="justify">También se puede usar una palabra cualquiera que haga las veces de código de barras, es decir, que esté escrita en los datos adicionales, todos los artículos que coincidan con dicha palabra serán agregados al carrito.</p>

 {{ Form::open(array('url' => 'cart/desde-archivo', 'files' => true)) }}

    <div class="input-group">
        {{ Form::file('articulos', array('class' => 'form-control', 'required')) }}
        <span class="input-group-btn">
            {{ Form::submit('Cargar', array('class' => 'btn btn-primary')) }}
        </span>
    </div>

 {{ Form::close() }}