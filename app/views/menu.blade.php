<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{{ url('/') }}">

      @if(Auth::check())
        {{ Auth::user()->roles()->first()->branch->name }}
      @else
        Inventarios
      @endif
    </a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li> {{ HTML::link('/', 'Inicio') }} </li>

      @if(Auth::check())

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Artículo <b class="caret"></b></a>
          <ul class="dropdown-menu">

            @if(Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))
              <li> {{ HTML::link('articles/add', 'Nuevo') }} </li>
            @endif

            <li> {{ HTML::link('articles', 'Listado') }}</li>

          </ul>
        </li>

        @if(Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))
          <li> {{ HTML::link('purchases', 'Compras') }} </li>
        @endif

      @endif

      @if(Auth::check() && Auth::user()->permitido('administrador'))

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Usuario <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li>
              {{ HTML::link('users/register', 'Nuevo') }}
            </li>
            <li>
              {{ HTML::link('users/list', 'Listado') }}
            </li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sucursal <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li>
              {{ HTML::link('branches/add', 'Nuevo') }}
            </li>
            <li>
              {{ HTML::link('branches', 'Listado') }}
            </li>
          </ul>
        </li>

      @endif

      @if(Auth::check())

        <li>
            {{ HTML::link('cart', 'Carrito') }}
          </li>

          <li>
            {{ HTML::link('users/change-password', 'Cambiar password') }}
          </li>

      @endif

    </ul><!-- /nav navbar-nav -->

    @if(Auth::check())

      <ul class="nav navbar-nav navbar-right">
        <li>
          <a href="{{ url('users/logout') }}" class="btn btn-link">
            <span class="glyphicon glyphicon-user"></span> Cerrar sesión
          </a>
        </li>
      </ul>

    @else

      {{ Form::open(array('url' => 'users/index', 'class' => 'navbar-form navbar-right')) }}
        <div class="form-group">
          {{ Form::email('email', (isset($email) ? $email : ''), array('class' => 'form-control', 'placeholder' => 'Email', 'required')) }}
          {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password', 'required')) }}
        </div>
        {{ Form::submit('Entrar', array('class' => 'btn btn-primary')) }}
      {{ Form::close() }}

    @endif

  </div><!-- /.navbar-collapse -->
</nav>