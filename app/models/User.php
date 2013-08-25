<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	protected $guarded = array();

    public static $rules = array();

	public static $messages = array(
        'name.required' => 'El nombre es requerido.',
        'password.required' => 'El password es requerido.',
        'email.required' => 'El email es requerido.',
        'email.email' => 'El email no es válido.'
    );

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function roles()
	{
		return $this->hasMany('Role');
	}

	public function permitido($rolname)
	{
		$roles = array();

		if($this) {
	    	foreach($this->roles as $rol) {
	        	array_push($roles, $rol->name);
	    	}
		}

		if(in_array($rolname, $roles)) {
			return true;
		}

		return false;
	}

	public function branches()
	{
		$branches = array();

		if($this) {
			foreach ($this->roles as $key => $value) {
				$branches[$key] = $value;
			}
		}

		return $branches;
	}

}