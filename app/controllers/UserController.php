<?php

class UserController extends BaseController {

	protected $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function getIndex()
	{
		$title = 'LogIn';

		return View::make('users.index')
			->with('title', $title);
	}

	public function postIndex()
	{
		$input = Input::all();

		$rules = array(
			'email' => 'required|email',
			'password' => 'required'
		);

		$v = Validator::make($input, $rules, User::$messages);

		if ($v->fails())
		{
			return Redirect::to('users/index')
					->withInput()
					->withErrors($v)
					->with('message');

		}
		else
		{
			$credentials = array(
				'email' => $input['email'],
				'password' => $input['password']
			);

			if (Auth::attempt($credentials))
			{
				$title = 'Inicio';
				Session::forget('message');

				return View::make('inicio')->with('title', $title);

			}
			else
			{
				$title = 'LogIn';
				$message = 'El email o el password introducidos no son correctos.';
				Session::flash('message', $message);

				return View::make('users.index')
					->with(array('title' => $title, 'email' => $input['email']));
			}
		}

	} #postIndex

	public function getRegister()
	{
		$title = "Registrar";
		$branches = Branche::all();

		return View::make('users.register')
		->with(compact('title', 'branches'));
	}

	public function postRegister()
	{
		$input = Input::all();

		$rules = array(
			'name' => 'required',
			'email' => 'required|unique:users|email',
			'password' => 'required'
		);

		$messages = array(
			'name.required' => 'El nombre es requerido',
			'email.required' => 'El email es requerido',
			'email.email' => 'El email no es válido',
			'email.unique' => 'El email ya existe',
			'password.required' => 'El password es requerido'
		);

		$v = Validator::make($input, $rules, $messages);

		if ($v->passes()) {
			$password = $input['password'];
			$password = Hash::make($password);

			$user = new User();
			$user->name = $input['name'];
			$user->email = $input['email'];
			$user->password = $password;

			try {
				$user->save();

			} catch (Exception $e) {
				$message = 'No se han guardado cambios porque hay otro usuario con ese nombre o email.';
        		Session::flash('message', $message);

        		return View::make('users.register')
		            ->with(compact('title', 'user'));
			}

			try {
				$rol = new Role();

				$r['name'] = $input['rol'];
				$r['branch_id'] = $input['branch'];
				$r['user_id'] = User::first()->orderBy('created_at', 'desc')->first()->id;

				$rol->create($r);

				return Redirect::to('users/list');

			} catch (Exception $e) {
				$message = 'No se ha podido asignar el rol al nuevo usuario.';
        		Session::flash('message', $message);

        		return View::make('users.register')
		            ->with(compact('title', 'user'));
			}


		} else {

			return Redirect::to('users/register')
			->withInput()
			->withErrors($v)
			->with('message');
		}
	}

	public function getEdit($id)
    {
    	$title = 'Editar Usuario';
		$branches = Branche::all();

		if(Auth::user() && (Auth::user()->permitido('administrador'))) {

	        $user = $this->user->find($id);

	        if (is_null($user))
	        {
	            return Redirect::to('user/index');
	        }

	        return View::make('users.edit')
	        	->with(compact('title', 'user', 'branches'));
	    }
    }

	public function postUpdate()
    {
    	$title = 'Editar usuario';
		$branches = Branche::all();

        $input = array_except(Input::all(), array('_method', 'password2'));
        $id = $input['id'];

        $rules = array(
			'name' => 'required',
			'email' => 'required|email',
			'password' => 'required'
		);

        $v = Validator::make($input, $rules, User::$messages);

        if ($v->passes())
        {
        	$password = $input['password'];
			$password = Hash::make($password);

	        $user = $this->user->find($id);
			$user->name = $input['name'];
			$user->email = $input['email'];
			$user->password = $password;

        	try {
            	$user->save();

        	} catch (Exception $e) {
        		// $message = $e->getMessage();
        		$message = 'No se han guardado cambios porque hay otro usuario con ese nombre o email.';
        		Session::flash('message', $message);

        		return View::make('users.edit')
		            ->with(compact('title', 'user', 'branches'));
        	}

        	try {
				$rol = $user->roles()->first();

				$r['name'] = $input['rol'];
				$r['branch_id'] = $input['branch'];
				$r['user_id'] = $user->id;

				$rol->update($r);

				return Redirect::to('users/list');

			} catch (Exception $e) {
				$message = 'No se ha podido asignar el rol al usuario <strong>'. $user->name .'</strong>.';
        		Session::flash('message', $message);

        		return View::make('users.register')
		            ->with(compact('title', 'user', 'branches'));
			}

            return Redirect::to('users/list');
        }

        return Redirect::to('users/edit/'. $id)
            ->withInput()
            ->withErrors($v)
            ->with('message', 'Hay errores de validación.');
    }

	public function getLogout()
	{
		Auth::logout();
		Session::flush();

		return Redirect::to('/');
	}

	public function getList()
	{
		$title = "Usuarios";

        $users = User::all();

		return View::make('users.list')
				->with(compact('users', 'title'));
	}

	public function getChangePassword()
	{
		$title = 'Cambiar password';

		return View::make('users.changePassword')
			->with(compact('title'));
	}

	public function postChangePassword()
	{
		$title = 'Cambiar password';
		$input = Input::all();

		try {
			if(Auth::attempt(array('email' => Auth::user()->email, 'password' => $input['password0'])))
			{
				if($input['password1'] == $input['password2'])
				{
					Auth::user()->password = Hash::make($input['password1']);
					Auth::user()->update();
				} else {
					Session::flash('message', 'Los passwords no coinciden.');

					return Redirect::to('users/change-password');
				}

			} else {
				Session::flash('message', 'Password actual incorrecto.');

				return Redirect::to('users/change-password');
			}

		} catch (Exception $e) {

			return Redirect::to('users/change-password');
		}

		Session::flash('message', 'Password cambiado con éxito.');

		return Redirect::to('users/change-password');
	}

}