<?php

class CartController extends BaseController {

	public function getIndex()
	{
		$title = 'Carrito';
		$cart = array();

		if (!Session::has('cart')) {
			return Redirect::to('articles');
		}

		return View::make('carts.index')
			->with(compact('title'));
	}

	public function postAdd()
	{
		$input = Input::all();
		$title = 'Agregar a carrito';

		$article = Article::find($input['id']);
		$cantidad = $input['cantidad'];

		self::addToCart($article, $cantidad);

		return Redirect::back();

		/*return View::make('carts.index')
			//->with(compact('title'));*/

	} #postAdd

	private function addToCart($article, $cantidad)
	{
		$cart = array();

		//Redondea a dos decimales la cantidad.
		$cantidad = round($cantidad, 2);

		if (Session::has('cart')) {
			$cart = Session::get('cart');
		}

		// Verifica si está en el carrito.
		if(empty($cart[$article->id]))
		{
			$cart[$article->id] = array('article' => $article, 'amount' => $cantidad);

		} else {

			// Suma la nueva cantidad
			$cart[$article->id]['amount'] += $cantidad;

		}

		Session::put('cart', $cart);

	} # addToCart

	public function getDesdeArchivo()
	{
		return View::make('carts.cargar');

	} #getDesdeArchivo

	public function postDesdeArchivo()
	{
		$articulosSinRegistrar = [];

		try {

			$extensiones = array('xls', 'xlsx');

			$file = Input::file("articulos");
			$extension = strtolower($file->getClientOriginalExtension());
			$size = Input::file('articulos')->getClientOriginalExtension();

			if(!in_array($extension, $extensiones))
			{
				return Redirect::to('/')
					->with('message', 'Tipo de archivo inválido.');
			}

			$dataUpload = array(
				"articulos" => $file
			);

			$objPHPExcel = PHPExcel_IOFactory::load($file);

			for ($i=2; $i < 200; $i++)
			{
				$codigoBarras = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue();
				$cantidad = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue();

				// Si la celda está vacía ignora la fila y continúa.
				if($codigoBarras == ''){
					continue;
				}

				// Se buscan los artículos que coincidan con el código de barras.
				$articulos = Article::whereRaw("comments LIKE '%". $codigoBarras ."%'")->get();
				$cantidad = $cantidad;

				// Si el artículo no existe en el sistema.
				if(empty($articulos->first()->name))
				{
					// Se agrega el código de barras al final del arreglo $articulosSinRegistrar.
					array_push($articulosSinRegistrar, $codigoBarras);

				} else {

					foreach ($articulos as $articulo)
					{
						// Se agrega el artículo al carrito.
						self::addToCart($articulo, $cantidad);
					}
				}

			} // For

			if(!empty($articulosSinRegistrar))
			{
				$mensaje = "No se encontraron artículos relacionados para los siguientes valores: <ul>";
				foreach ($articulosSinRegistrar as $articulo) {
					$mensaje .= "<li>". $articulo ."</li>";
				}
				$mensaje .= "</ul>";

				return Redirect::to('cart')
					->with('message', $mensaje);

			}

			return Redirect::to('cart')
					->with('messageOk', 'Artículos agregados al carrito satisfactoriamente.');

		} catch (Exception $e)
		{
			return Redirect::to('/')
					->with('message', 'Ocurrió un error en el postDesdeArchivo.');
		}

	} #postDesdeArchivo

	public function getClear()
	{
		Session::forget('cart');

		return Redirect::to('cart');
	}

	public function getClearItem($idArticle)
	{
		$title = 'Quitar item';
		$cart = array();

		if (Session::has('cart')) {
			$cart = Session::get('cart');
		}

		unset($cart[$idArticle]);

		Session::put('cart', $cart);

		return View::make('carts.index')
			->with(compact('title'));

	}

}