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

        $branches = Branche::all();

		return View::make('articles.index')
				->with(compact('articles', 'title', 'branches'));
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
	} #postAdd

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
    } #getEdit

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
    } #postUpdate

    public function postSearch()
    {
        $title = "Artículos";
        $input = Input::all();

        Session::flash('filtro', 'Resultados con <strong>'. $input['search'] .'</strong>');

        $articles = Article::whereRaw("id = '". $input['search'] ."' OR name like '%". $input['search'] ."%'")->orderBy('name', 'asc')->paginate(5);

        $branches = Branche::all();

        return View::make('articles.index')
                ->with(compact('articles', 'title', 'branches'));
    } #postSearch

    public function getImage($idArticle)
    {
        $title = 'Imagen';

        $article = Article::find($idArticle);

        return View::make('articles.changeImage')
            ->with(compact('title', 'article'));
    } #getImage

    public function postImage()
    {
        $file = Input::file("image");
        $idArticle = Input::get('idArticle');

        $dataUpload = array(
            "image" => $file
        );

        $rules = array(
            'image' => 'required|image:jpg,gif,png|max:1000'
        );

        $messages = array(
            'required' => 'El campo :attribute es obligatorio.',
            'image.max' => 'El archivo no puede ser mayor de 1 MB.',
        );

        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails())
        {
            return Redirect::to('articles')->withErrors($validation);

        }else{

            $articleImage = ArticleImage::find($idArticle);
            $filename = $file->getClientOriginalName();
            $fileInfo = new SplFileInfo($filename);
            $filename = $idArticle .'.'. $fileInfo->getExtension();

            $ai['id'] = $idArticle;
            $ai['user_id'] = Auth::user()->id;
            $ai['image'] = $filename;

            if(empty($articleImage)){

                $articleImage = new ArticleImage();
                $articleImage->create($ai);

                $file->move("img/articles", $filename);

                return Redirect::to('articles')->with(array('messageOk' => 'Imagen subida con éxito.'));

            } elseif($articleImage->update($ai)) {

                $file->move("img/articles", $filename);

                return Redirect::to('articles')->with(array('messageOk' => 'Imagen actualizada con éxito.'));
            }
        }

    } #postImage

}
