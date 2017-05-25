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

		$articles = Article::orderBy('name', 'asc')->paginate(6);

		$branches = Branche::orderBy('name', 'asc')->get();

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

		if(Auth::user() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))
		{
			$input = Input::all();

			//Redondea a dos decimales el costo
			$input['cost'] = round($input['cost'], 2);

			//Redondea a dos decimales el precio
			$input['price'] = round($input['price'], 2);

			$v = Validator::make($input, Article::$rules, Article::$messages);

			if ($v->passes())
			{
				$idArticle = 1;

				try
				{
					$this->article->create($input);

					$input['id'] = Article::first()->orderBy('created_at', 'desc')->first()->id;
					$idArticle = $input['id'];

					self::logChanges(array_except($input, '_token'));

				}
				catch (Exception $e)
				{
					// $message = $e->getMessage();
					$message = 'No se ha podido guardar el nuevo artículo, quizá exista otro artículo con ese nombre.';
					Session::flash('message', $message);

					return Redirect::to('articles/add')
					->withInput();
				}

				return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('messageOk' => 'Artículo creado con éxito.'));
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

		if(Auth::user() && (Auth::user()->permitido('administrador') || Auth::user()->permitido('remisionero')))
		{
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

		//Redondea a dos decimales el costo
		$input['cost'] = round($input['cost'], 2);

		//Redondea a dos decimales el precio
		$input['price'] = round($input['price'], 2);

		$v = Validator::make($input, Article::$rules, Article::$messages);

		if ($v->passes())
		{
			$article = $this->article->find($id);

			try {
				$article->update($input);
				self::logChanges(array_except($input, '_token'));

			} catch (Exception $e) {
				// $message = $e->getMessage();
				$message = 'No se han guardado cambios porque hay otro artículo con ese nombre.';
				Session::flash('message', $message);

				return View::make('articles.edit')
					->with(compact('title', 'article'));
			}

			return Redirect::to('articles/search?filterBy=id&search='. $id)->with(array('messageOk' => 'Artículo editado con éxito.'));
		}

		return Redirect::to('articles/edit/'. $id)
			->withInput()
			->withErrors($v)
			->with('message', 'Hay errores de validación.');

	} #postUpdate

	private function logChanges($input)
	{

		$log = json_encode($input);

		DB::table('article_changes')->insert(
			array(
				'article_id' => $input['id'],
				'log' => $log,
				'user_id' => Auth::user()->id,
				'created_at' => new Datetime())
		);

	} #logChanges

	public function getShowChanges($idArticle)
	{
        if(Auth::user()->permitido('vendedor')) {
            App::abort(403, 'Unauthorized action.');
        }

		$article = DB::table('article_changes')
			->where('article_id', '=', $idArticle)
			->orderBy('created_at', 'desc')
			->paginate(6);

		return View::make('articles.changes')
			->with(compact('article'));

	} #getShowChanges

	public function getSearch()
	{
		$title = "Artículos";
		$input = Input::all();
		$articles = null;
		$filtro = '';

		if($input['filterBy'] == 'id') {

			$filtro = 'Artículo con código <strong>'. $input['search'] .'</strong>.';

			$articles = Article::whereRaw("id = '". $input['search'] ."'")->paginate(6);

		}
		else if($input['filterBy'] == 'comments')
		{
			$palabras = explode(' ', $input['search']);

			$articles = Article::where('comments', 'LIKE', '%%');
			foreach ($palabras as $palabra)
			{
				$articles->where('comments', 'LIKE', '%'. $palabra .'%');
			}
			$articles = $articles->orderBy('comments', 'ASC')->paginate(6);

			$filtro = 'Artículos que contienen en datos adicionales <strong>'. $input['search'] .'</strong>.';
			//$articles = Article::whereRaw("comments like '%". $input['search'] ."%'")->orderBy('name', 'asc')->paginate(6);
		}
		else
		{ // Se asume que el filtro es por nombre.
			$palabras = explode(' ', $input['search']);

			$articles = Article::where('name', 'LIKE', '%%');
			foreach ($palabras as $palabra)
			{
				$articles->where('name', 'LIKE', '%'. $palabra .'%');
			}
			$articles = $articles->orderBy('name', 'ASC')->paginate(6);

			$filtro = 'Artículos que contienen en el nombre <strong>'. $input['search'] .'</strong>.';
			//$articles = Article::whereRaw("name like '%". $input['search'] ."%'")->orderBy('name', 'asc')->paginate(6);
		}


		$branches = Branche::orderBy('name', 'asc')->get();

		return View::make('articles.index')
				->with(compact('articles', 'title', 'branches', 'filtro', 'input'));

	} #getSearch

	public function getImage($idArticle)
	{
		$title = 'Imagen';

		$article = Article::find($idArticle);

		return View::make('articles.changeImage')
			->with(compact('title', 'article'));
	} #getImage

	public function postImage()
	{
		$idArticle = Input::get('idArticle');

		try
		{
			$extensiones = array('jpg', 'jpeg', 'gif', 'png', 'bmp');

			$file = Input::file("image");
			$extension = strtolower($file->getClientOriginalExtension());
			$size = Input::file('image')->getClientOriginalExtension();

			if (!in_array($extension, $extensiones))
			{
				return Redirect::to('articles')
					->with('message', 'Tipo de archivo inválido.');
			}

			if ($file->getSize() > 10000000)
			{
				return Redirect::to('articles/search?filterBy=id&search='. $idArticle)
					->with('message', 'El tamaño de la imagen no puede ser mayor a 10000KB.');
			}

			$dataUpload = array(
				"image" => $file
			);

			$rules = array(
				/*'image' => 'required|image:jpg,jpeg,gif,png|max:1000'*/
			);

			$messages = array(
				/*'required' => 'El campo :attribute es obligatorio.',
				'image.max' => 'El archivo no puede ser mayor de 1 MB.'*/
			);

			$validation = Validator::make(Input::all(), $rules, $messages);


			// $filename = $file->getClientOriginalName();
			// $fileInfo = new SplFileInfo($filename);
			// $filename = $idArticle .'.'. $fileInfo->getExtension();


			if ($validation->fails())
			{
				return Redirect::to('articles')->withErrors($validation);

			}
			else
			{
				$articleImage = ArticleImage::find($idArticle);

				$ai['id'] = $idArticle;
				$ai['user_id'] = Auth::user()->id;
				$ai['image'] = $idArticle .'.'. $extension;

				if (empty($articleImage))
				{
					$articleImage = new ArticleImage();
					$articleImage->create($ai);

					/*Cambia el tamaño de la imagen y guarda el archivo en img/articles con el id del artículo y su extenxión*/
					Image::make($file->getRealPath())->widen(150)->save('img/articles/'. $ai['image']);
					// $file->move("img/articles", $ai['image']);

					return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('messageOk' => 'Imagen subida con éxito.'));

				}
				else if ($articleImage->update($ai))
				{
					Image::make($file->getRealPath())->widen(150)->save('img/articles/'. $ai['image']);
					// $file->move("img/articles", $ai['image']);

					return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('messageOk' => 'Imagen actualizada con éxito.'));
				}

			} #else

		}
		catch (Exception $e)
		{
			return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('message' => "<p>$e</p>".'<p>La imagen no se pudo subir, revisa el formato (jpg,jpeg,gif,png) y el tamaño del archivo (max:1000KB).</p>'));
		}

	} #postImage

	public function getQuitarImagen($idArticle)
	{

		try
		{
			$articleImage = ArticleImage::find($idArticle);

			if(!empty($articleImage))
			{
				/*Quita el registro de la base de datos*/
				$articleImage->delete();
				/*Elimina la imagen del disco duro*/
				File::delete(public_path() .'/img/articles/'. $articleImage->image);
			}

			return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('messageOk' => 'Imagen del artículo '. $idArticle .' eliminada con éxito.'));

		}
		catch (Exception $e)
		{
				return Redirect::to('articles/search?filterBy=id&search='. $idArticle)->with(array('message' => 'No fue posible eliminar la imagen del artículo '. $idArticle));
		}

	} #getQuitarImagen

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
					->setCellValue('A2', 'Nombre de artículo')
					->setCellValue('B2', $article->name)
					->setCellValue('A3', 'Unidad de medida')
					->setCellValue('B3', $article->unit)
					->setCellValue('A4', 'Precio unitario')
					->setCellValue('B4', $article->price)
					->setCellValue('A5', 'Costo unitario')
					->setCellValue('B5', $article->cost);

		// Encabezados con UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A6', 'Código de sucursal')
					->setCellValue('B6', 'Nombre de sucursal')
					->setCellValue('C6', 'Stock')
					->setCellValue('D6', 'Precio neto')
					->setCellValue('E6', 'Costo neto');

		$stocks = Stock::where('article_id', '=', $article->id)->get();

		// Desde donde inicia el contenido.
		$fila = 7;

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
		$objPHPExcel->getActiveSheet()->setTitle('a' . $article->id . date('_Ymd'));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
		/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="a'. $article->id . date('_YmdHis') .'.xlsx"');
		header('Cache-Control: max-age=0');*/

		$nombre_archivo = 'a' . $article->id . date('_His') . '.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(public_path() .'/excel/'. $nombre_archivo);

		return Redirect::to('articles/search?filterBy=id&search='. $idArticle)
			->with(array('mensajewell' => 'El archivo '. $nombre_archivo .' se ha creado con éxito, si la descarga no inicia automáticamente haga click <a id="descarga" href="'. url('excel/'. $nombre_archivo) .'">aquí</a> para descargarlo.<script>$(document).on("ready", function(){  $(location).attr("href", "'. url('excel/'. $nombre_archivo) .'");  });</script>'));

		// Esta línea impide que funcione en mac con XAMPP.
		//$objWriter->save('php://output');

		//exit;

	} #getExcelByArticle

	public function getMovimientos($idArticle)
	{
		$articulo = Article::find($idArticle);

		if(!empty($articulo))
		{
			$fecha1 = Input::get('fecha1');
			$fecha2 = Input::get('fecha2');
			$nombreArticulo = $articulo->name;
			$movimientos = $articulo->movimientos($fecha1, $fecha2);

			return View::make('articles.movimientos')
				->with(compact('movimientos', 'articulo'));
		}

		return Redirect::to('/')->with('message', 'No se encontraron movimientos para el artículo indicado.');

	} #movimientos

	public function getExcelConMovimientos($idArticle)
	{
		$article = Article::find($idArticle);

		if(empty($article))
		{
			return Redirect::to('articles');
		}

		$fecha1 = Input::get('fecha1');
		$fecha2 = Input::get('fecha2');
		$movimientos = $article->movimientos($fecha1, $fecha2);

		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('America/Bogota');

		if (PHP_SAPI == 'cli')
			die('Este archivo corre únicamente desde un navegador web.');

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator(Auth::user()->name)
									 ->setLastModifiedBy(Auth::user()->name)
									 ->setTitle("Movimientos de artículo")
									 ->setSubject("Artículo ". $article->name)
									 ->setDescription("Este documento contiene los movimientos del artículo ". $article->name)
									 ->setKeywords("artículo, sucursal, ". $article->name)
									 ->setCategory("Archivo generado");

		// Datos de encabezado
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Movimientos del artículo '. $article->name .' desde '. $fecha1 .' hasta '. $fecha2)
					->setCellValue('A2', 'Fecha')
					->setCellValue('B2', 'Tipo de remisión')
					->setCellValue('C2', 'Id remisión')
					->setCellValue('D2', 'Sucursal')
					->setCellValue('E2', 'Cantidad')
					->setCellValue('F2', 'Estado')
					->setCellValue('G2', 'Comentario del remisionero')
					->setCellValue('H2', 'Nota reciente')
					->setCellValue('I2', 'Saldo (opcional)');

		$stocks = Stock::where('article_id', '=', $article->id)->get();

		// Desde donde inicia el contenido.
		$fila = 3;

		foreach ($movimientos as $movimiento) {

			$fecha = (string) $movimiento['fecha'];

			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'. $fila, $fecha)
					->setCellValue('B'. $fila, $movimiento['tipo'])
					->setCellValue('C'. $fila, $movimiento['id'])
					->setCellValue('D'. $fila, $movimiento['sucursal'])
					->setCellValue('E'. $fila, $movimiento['cantidad'])
					->setCellValue('F'. $fila, $movimiento['estado'])
					->setCellValue('G'. $fila, $movimiento['comentario'])
					->setCellValue('H'. $fila, $movimiento['nota']);

			$fila++;
		}

		$formula = 'SI(Y(B4="compra";F4<>"cancelado");I3+E4;SI(Y(O(B4="venta";B4="daño";B4="entrega inmediata");F4<>"cancelado");I3-E4;I3))';

		//Valor inicial de saldo
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('I3', '0')
				->setCellValue('I4', (string) $formula);

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('m' . $article->id . date('_Ymd'));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		$nombre_archivo = 'm' . $article->id . date('_His') . '.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(public_path() .'/excel/'. $nombre_archivo);

		return Redirect::to('articles/search?filterBy=id&search='. $idArticle)
			->with(array('mensajewell' => 'El archivo '. $nombre_archivo .' se ha creado con éxito, si la descarga no inicia automáticamente haga click <a id="descarga" href="'. url('excel/'. $nombre_archivo) .'">aquí</a> para descargarlo.<script>$(document).on("ready", function(){ $(location).attr("href", "'. url('excel/'. $nombre_archivo) .'"); });</script>'));

		//Formula para calcular columna de saldo
		/*
		=SI(
			Y(B4="compra";F4<>"cancelado");
				I3+E4;
					SI(
						Y( O(B4="venta";B4="daño";B4="entrega inmediata"); F4<>"cancelado");
							I3-E4;
								I3
					)
		)

		=SI(Y(B4="compra";F4<>"cancelado");I3+E4;SI(Y(O(B4="venta";B4="daño";B4="entrega inmediata");F4<>"cancelado");I3-E4;I3))
		*/

	} #getExcelArticuloMovimientos

} #ArticleController
