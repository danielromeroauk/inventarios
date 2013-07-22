<?php

class UserController extends BaseController {

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

		$messages = array(
			'email.required' => 'El email es requerido',
			'email.email' => 'El email no es válido',
			'password.required' => 'El password es requerido',
		);

		$v = Validator::make($input, $rules, $messages);

		if ($v->fails()) {

			return Redirect::to('users/index')
					->withInput()
					->withErrors($v)
					->with('message');

		} else {
			$credentials = array(
				'email' => $input['email'],
				'password' => $input['password']
			);

			if (Auth::attempt($credentials)) {
				$title = 'Perfil';

				return View::make('users.profile')->with('title', $title);

			} else {
				$title = 'LogIn';
				$message = 'El email o el password introducidos no son correctos.';

				return View::make('users.index')
					->with(array('title' => $title, 'email' => $input['email'], 'message' => $message));

				// return View::make('users/index')
				// 	->with(array('message' => $message, 'title' => $title))
				// 	->withInput();
			}
		}
	}

	public function getRegister()
	{
		$title = "Registrar";
		return View::make('users.register')
		->with('title', $title);
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
			$user->save();

			return Redirect::to('/');

		} else {

			return Redirect::to('users/register')
			->withInput()
			->withErrors($v)
			->with('message');
		}
	}

	public function getProfile()
	{
		$title = ucwords("Perfil de " . Auth::user()->name);
		return View::make('user.profile')
		->with('title', $title);
	}

	public function getLogout()
	{
		Auth::logout();

		return Redirect::to('/');
	}

}