<div class="navbar">
  <div class="container">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    @if(Auth::check())
      {{ HTML::link('/', Auth::user()->roles()->first()->branch->name, array('class' => 'navbar-brand')) }}
    @else
      {{ HTML::link('/', 'Inventarios', array('class' => 'navbar-brand')) }}
    @endif
    <div class="nav-collapse collapse">
      @if(Auth::check())

        <ul class="nav navbar-nav">
          <li>
            {{ HTML::link('/', 'Inicio') }}
          </li>

          @if(Auth::user()->permitido('administrador'))

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Artículo <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                    {{ HTML::link('articles/add', 'Nuevo') }}
                </li>
                <li>
                    {{ HTML::link('articles', 'Listado') }}
                </li>
              </ul>
            </li>

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

        <li>
          {{ HTML::link('users/change-password', 'Cambiar password') }}
        </li>

        </ul>
        <div class="pull-right">
            {{ HTML::link('users/logout', 'Cerrar sesión', array('class' => 'btn btn-link')) }}
        </div>

      @else

        {{ Form::open(array('url' => 'users/index', 'class' => 'navbar-form form-inline pull-right')) }}
          {{ Form::email('email', (isset($email) ? $email : ''), array('class' => 'form-control', 'placeholder' => 'Email', 'required')) }}
          {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password', 'required')) }}
          {{ Form::submit('Entrar', array('class' => 'btn btn-primary')) }}
        {{ Form::close() }}

      @endif
    </div><!--/.nav-collapse -->

  </div><!--/.container -->
</div><!--/.navbar -->
