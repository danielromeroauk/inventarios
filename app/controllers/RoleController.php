<?php

class RoleController extends BaseController {

	protected $rol;

	public function __construct(Role $rol)
	{
		$this->rol = $rol;
	}

}