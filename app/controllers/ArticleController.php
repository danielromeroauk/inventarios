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

	            return Redirect::to('articles');
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
	            return Redirect::to('articles');
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

    public function getExcelByArticle($idArticle)
    {
        $article = Article::find($idArticle);

        if(empty($article))
        {
            return Redirect::to('articles');
        }

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('America/Bogota');

        if (PHP_SAPI == 'cli')
            die('Este archivo corre únicamente desde un navegador web.');

        /** Include PHPExcel */
        require_once app_path() . '\..\vendor\phpoffice\phpexcel\Classes\PHPExcel.php';


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator(Auth::user()->name)
                                     ->setLastModifiedBy(Auth::user()->name)
                                     ->setTitle("Informe de artículo")
                                     ->setSubject("Artículo ". $article->name)
                                     ->setDescription("Este documento contiene el stock por sucursal del artículo ". $article->name)
                                     ->setKeywords("artículo, sucursal, ". $article->name)
                                     ->setCategory("Archivo generado");


        // Datos de sucursal
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Código de artículo')
                    ->setCellValue('B1', $article->id)
                    ->setCellValue('A2', 'Precio unitario')
                    ->setCellValue('B2', $article->price)
                    ->setCellValue('A3', 'Costo unitario')
                    ->setCellValue('B3', $article->cost);

        // Encabezados con UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A4', 'Código de sucursal')
                    ->setCellValue('B4', 'Nombre de sucursal')
                    ->setCellValue('C4', 'Stock')
                    ->setCellValue('D4', 'Precio neto')
                    ->setCellValue('E4', 'Costo neto');

        $stocks = Stock::where('article_id', '=', $article->id)->get();
        $fila = 5;
        foreach ($stocks as $stock) {

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'. $fila, $stock->branch->id)
                    ->setCellValue('B'. $fila, $stock->branch->name)
                    ->setCellValue('C'. $fila, $stock->stock)
                    ->setCellValue('D'. $fila, ($stock->article->price * $stock->stock))
                    ->setCellValue('E'. $fila, ($stock->article->cost * $stock->stock));
            $fila++;
        }


        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($article->name . date('Y-m-d'));


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $article->name . date('_Y-m-d_His') .'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    } #getExcelByArticle

}
