<?php

class BrancheController extends BaseController {

	protected $branch;

	public function __construct(Branche $branch)
	{
		$this->branch = $branch;
	}

	public function getIndex()
	{
		$title = "Sucursales";

        $branches = Branche::all();

		return View::make('branches.index')
				->with(compact('branches', 'title'));
	}

	public function getAdd()
	{
		$title = "Nueva sucursal";

		return View::make('branches.add')
				->with('title', $title);
	}

	public function postAdd()
	{
    	$title = 'Nueva sucursal';

		if(Auth::user() && (Auth::user()->permitido('administrador'))) {
			$input = Input::all();

			$v = Validator::make($input, Branche::$rules, Branche::$messages);

	        if ($v->passes())
	        {
	        	try {
	            $this->branch->create($input);

	        	} catch (Exception $e) {
	        		// $message = $e->getMessage();
	        		$message = 'No se ha podido guardar la nueva sucursal, quizá exista otra con ese nombre.';
	        		Session::flash('message', $message);

	        		return Redirect::to('branches/add')
        			->withInput();
	        	}

	            return Redirect::to('branches/index');
	        }

	        return Redirect::to('branches/add')
	            ->withInput()
	            ->withErrors($v)
	            ->with('message');
		}
	}

	public function getEdit($id)
    {
    	$title = 'Editar Sucursal';

		if(Auth::user() && (Auth::user()->permitido('administrador'))) {

	        $branch = $this->branch->find($id);

	        if (is_null($branch))
	        {
	            return Redirect::to('branches/index');
	        }

	        return View::make('branches.edit')
	        	->with(compact('title', 'branch'));
	    }
    }

	public function postUpdate()
    {
    	$title = 'Editar Sucursal';
        $input = array_except(Input::all(), '_method');
        $id = $input['id'];

        $v = Validator::make($input, Branche::$rules, Branche::$messages);

        if ($v->passes())
        {
            $branch = $this->branch->find($id);

        	try {
            	$branch->update($input);

        	} catch (Exception $e) {
        		// $message = $e->getMessage();
        		$message = 'No se han guardado cambios porque hay otra sucursal con ese nombre.';
        		Session::flash('message', $message);

        		return View::make('branches.edit')
		            ->with(compact('title', 'branch'));
        	}

            return Redirect::to('branches/index');
        }

        return Redirect::to('branches/edit/'. $id)
            ->withInput()
            ->withErrors($v)
            ->with('message', 'Hay errores de validación.');
    }

}