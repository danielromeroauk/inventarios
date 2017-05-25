<div class="navbar navbar-default" id="encabezado">
    <a class="navbar-brand" href="{{ url('/') }}">
      @if(Auth::check())
        {{ Auth::user()->roles()->first()->branch->name }}
      @else
        Inventarios
      @endif
    </a>

    @if(Auth::check())

      <ul class="nav navbar-nav pull-right">
        <p class="navbar-text">
          <span class="glyphicon glyphicon-time"></span>
          <span id="contador"></span>
        </p>
          <p class="navbar-text">
            <span class="glyphicon glyphicon-user"></span>
            {{ Auth::user()->email }}
          </p>
        <li>
          <a href="{{ url('users/logout') }}" >
            <span class="glyphicon glyphicon-log-out"></span> Salir &nbsp;
          </a>
        </li>
      </ul>

    @else

      {{ Form::open(array('url' => 'users/index', 'class' => 'navbar-form navbar-right')) }}
        <div class="form-group">
          {{ Form::email('email', (isset($email) ? $email : ''), array('class' => 'form-control', 'placeholder' => 'Email', 'required', 'autofocus')) }}
          {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password', 'required')) }}
        </div>
        {{ Form::submit('Entrar', array('class' => 'btn btn-primary')) }}
      {{ Form::close() }}

    @endif

</div>

@if(Auth::check())

  <nav class="navbar navbar-default" role="navigation">

    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only">Toggle navigación</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">

      <ul class="nav navbar-nav">

        <li>
          {{ '<a href="'. url('/') .'">
                  <span class="glyphicon glyphicon-home"></span>
                  inicio
              </a>' }}
        </li>

        <li>
          {{ '<a href="'. url('articles') .'">
                  <span class="glyphicon glyphicon-list-alt"></span>
                  Artículos
              </a>' }}

        @if(Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Registrar <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li> {{ HTML::link('articles/add', 'Artículo') }} </li>

              @if(Auth::user()->permitido('administrador'))
                <li> {{ HTML::link('users/register', 'Usuario') }} </li>
                <li> {{ HTML::link('branches/add', 'Sucursal') }} </li>
              @endif

            </ul>
          </li>
        @endif

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Informes <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li> {{ HTML::link('purchases', 'Compras') }} </li>
            <li> {{ HTML::link('sales', 'Ventas') }} </li>
            <li> {{ HTML::link('damages', 'Daños') }} </li>
            <li> {{ HTML::link('instants', 'Entregas inmediatas') }} </li>
            <li> {{ HTML::link('rotations', 'Rotaciones') }} </li>
          </ul>
        </li>

        @if(Auth::user()->permitido('administrador'))
          <li> {{ HTML::link('users/list', 'Usuarios') }} </li>
        @endif

        @if(!Auth::user()->permitido('vendedor'))
          <li> {{ HTML::link('branches', 'Sucursales') }} </li>
        @endif

        <li> {{ HTML::link('users/change-password', 'Cambiar password') }} </li>

        <li>
            @if(Session::has('cart'))

              <?php $carrito = Session::get('cart'); ?>

              {{ '<a href="'. url('cart') .'">
                <span class="glyphicon glyphicon-shopping-cart"></span>
                Ver Carrito
                <span class="badge">'. count($carrito) .'</span>
                </a>' }}

            @endif
        </li>

      </ul><!-- /nav navbar-nav -->

    </div><!-- /.navbar-collapse -->

  </nav><!-- /.navbar navbar-default -->

@endif