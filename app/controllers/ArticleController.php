<?php

class ArticleController extends BaseController {

	protected $article;

	public function __construct(Article $article)
	{
		$this->article = $article;
	}

	public function getIndex()
	{
		$title = "Artículos";

        $articles = Article::orderBy('name', 'asc')->paginate(5);

		return View::make('articles.index')
				->with(compact('articles', 'title'));
	}

	public function getAdd()
	{
		$title = "Nuevo artículo";

		return View::make('articles.add')
				->with('title', $title);
	}

	public function postAdd()
	{
    	$title = 'Nuevo artículo';

		if(Auth::user() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))) {
			$input = Input::all();

			$v = Validator::make($input, Article::$rules, Article::$messages);

	        if ($v->passes())
	        {
	        	try {
	            $this->article->create($input);

	        	} catch (Exception $e) {
	        		// $message = $e->getMessage();
	        		$message = 'No se ha podido guardar el nuevo artículo, quizá exista otro artículo con ese nombre.';
	        		Session::flash('message', $message);

	        		return Redirect::to('articles/add')
        			->withInput();
	        	}

	            return Redirect::to('articles/index');
	        }

	        return Redirect::to('articles/add')
	            ->withInput()
	            ->withErrors($v)
	            ->with('message');
		}
	}

	public function getEdit($id)
    {
    	$title = 'Editar artículo';

		if(Auth::user() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero'))) {

	        $article = $this->article->find($id);

	        if (is_null($article))
	        {
	            return Redirect::to('articles/index');
	        }

	        return View::make('articles.edit')
	        	->with(compact('title', 'article'));
	    }
    }

	public function postUpdate()
    {
    	$title = 'Editar artículo';
        $input = array_except(Input::all(), '_method');
        $id = $input['id'];

        $v = Validator::make($input, Article::$rules, Article::$messages);

        if ($v->passes())
        {
            $article = $this->article->find($id);

        	try {
            	$article->update($input);

        	} catch (Exception $e) {
        		// $message = $e->getMessage();
        		$message = 'No se han guardado cambios porque hay otro artículo con ese nombre.';
        		Session::flash('message', $message);

        		return View::make('articles.edit')
		            ->with(compact('title', 'article'));
        	}

            return Redirect::to('articles/index');
        }

        return Redirect::to('articles/edit/'. $id)
            ->withInput()
            ->withErrors($v)
            ->with('message', 'Hay errores de validación.');
    }

}