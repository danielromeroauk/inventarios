<?php

class SaleController extends BaseController {

	const TIPO_REMISION = 'sales';

	public function getIndex()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$sales = Sale::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Ventas con estado <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'TIPO_REMISION'));
	}

	public function postAdd()
	{
		$title = 'Proceso de venta';
		$cart = array();
		$input = Input::all();

		if (Session::has('cart'))
		{
			$cart = Session::get('cart');
		}

		if (empty($cart)) {
			return Redirect::to('sales');
		}

		foreach ($cart as $item)
		{
			$check = Article::checkStock($item['article'], $input['branch_id'], $item['amount'], 'venta');

			/*Comprueba si hay suficiente stock en la sucursal*/
			if ($check != 'Ok') {
				Session::flash('message', $check);

				return Redirect::to('cart');
			}

		}

		/*Crea el registro en la tabla sales*/
		$sale = self::saveInSaleTable();

		/*Crea los registros en la tabla sale_items*/
		foreach ($cart as $item)
		{
			self::saveInSaleItemTable($item['article']->id, $item['amount'], $sale->id);

		} #foreach $cart as $item

		/*Vacía el carrito*/
		Session::forget('cart');

		return Redirect::to('sales/items/'. $sale->id);

	} #postSale

	private function saveInSaleTable()
	{
		try
		{
			$input = Input::all();
			$saleTable = new Sale();

			$saleTable->user_id = Auth::user()->id;
			$saleTable->branch_id = $input['branch_id'];

			$saleTable->comments = $input['fechareal'] .' '
				. $input['documento'] .' '
				. $input['nombre'] .' '
				. $input['nit'] .' '
				. $input['direccion'];

			$saleTable->status = 'pendiente';

			$saleTable->save();

			return $saleTable;

		}
		catch (Exception $e)
		{
			die('No se pudo guardar el registro en ventas.');
		}

	} #saveInSaleTable

	private function saveInSaleItemTable($idArticle, $amount, $idSale)
	{
		try
		{
			$saleItemsTable = new SaleItem();
			$saleItemsTable->sale_id = $idSale;
			$saleItemsTable->article_id = $idArticle;
			$saleItemsTable->amount = $amount;

			$saleItemsTable->save();

		}
		catch (Exception $e)
		{
			die($e .'No se pudo guardar el articulo '. $idArticle .' como item de la venta.');
		}

	} #saveInSaleItemTable

	private function saveInStockTable($idBranch, $idArticle, $amount)
	{
		try {

			// $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();
			// $articleStock = Stock::where('article_id', '=', $idArticle)->where('branch_id', '=', $idBranch)->first();
			$articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

			if(!empty($articleStock))
			{
				$articleStock->stock -= $amount;
				$articleStock->save();

			} #if !empty($ArticleStock)

		} catch (Exception $e) {
			die('No se pudo modificar el stock del artículo'. $idArticle .' en la sucursal '. $idBranch);
		}
	}

	public function getItems($idSale)
	{
		$title = 'Items de la venta';
		$sale = Sale::find($idSale);
		$sitems = SaleItem::where('sale_id', '=', $idSale)->get();

		return View::make('sales.items')
			->with(compact('title', 'sale', 'sitems'));
	}

	public function postSaleStore()
	{
		try
		{
			$input = Input::all();
			$sale = Sale::find($input['sale']);

			// Si no existe notaparcial y está pendiente es porque se está finalizando la remisión.
			if(!isset($input['notaparcial']) && $sale->status == 'pendiente')
			{
				$sitems = SaleItem::where('sale_id', '=', $input['sale'])->get();

				foreach ($sitems as $sitem) {
					self::saveInStockTable($input['branch_id'], $sitem->article->id, $sitem->amount);
				} #foreach

				/*Cambiar el status en la tabla sale a finalizado*/
				$sale = Sale::find($input['sale']);
				$sale->status = 'finalizado';
				$sale->save();

			}

			$saleStore = new SaleStore();
			$saleStore->sale_id = $input['sale'];
			$saleStore->user_id = Auth::user()->id;
			$saleStore->comments = $input['comments'];
			$saleStore->save();

			return Redirect::to('sales/items/'. $input['sale']);

		}
		catch (Exception $e)
		{
			die('No se pudo disminuir el stock.');
		}

	} #postSaleStore

	public function getCancel($idSale)
	{
		try
		{
			$sale = Sale::find($idSale);

			if ($sale->status != 'finalizado')
			{
				$sale->status = 'cancelado';
				$sale->save();

			}

			return Redirect::to('sales/items/'. $idSale);

		}
		catch (Exception $e)
		{
			die('No fue posible cancelar la venta.');
		}

	} #getCancel

	public function getFilterByStatus()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();

		$sales = Sale::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Ventas con estado <strong>'. $input['estado'] .'</strong>';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByStatus

	public function getFilterByStatusBranch()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();

		$branch = Branche::find($input['branch_id']);

		$sales = Sale::where('status', '=', $input['estado'])->where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Ventas con estado <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByStatusBranch

	public function getFilterById()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();

		$sales = Sale::where('id', '=', $input['idRemision'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Venta con código <strong>'. $input['idRemision'] .'</strong>';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterById

	public function getFilterByArticle()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();
		$idsSale = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->saleItems as $sitem) {
				$idsSale .= $sitem->sale->id .',';
				$amounts[$sitem->sale->id] = $sitem->amount;
			}

		}

		$idsSale = trim($idsSale, ',');

		$sales = Sale::whereRaw('id in ('. $idsSale .')')->orderBy('id', 'desc')->paginate(50);

		$mensaje = 'Ventas que contienen el artículo <strong>'. $articleName .'</strong>';

		return View::make('sales.list')
				->with(compact('title', 'sales', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByArticle

	public function getFilterByDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();

		$sales = Sale::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Ventas con fecha de creación entre <strong>'. Input::get('fecha1') .'</strong> y <strong>'. Input::get('fecha2') .'</strong>';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByDates

	public function getFilterByArticleDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();
		$idsSale = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->saleItems as $sitem) {
				$idsSale .= $sitem->sale->id .',';
				$amounts[$sitem->sale->id] = $sitem->amount;
			}

		}

		$idsSale = trim($idsSale, ',');

		$sales = Sale::whereRaw('id in ('. $idsSale .')')
			->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
			->where('status', '<>', 'cancelado')
			->orderBy('id', 'asc')->paginate(50);

		$mensaje = 'Ventas entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>, este filtro no tiene en cuenta las remisiones que están canceladas.';

		return View::make('sales.list')
				->with(compact('title', 'sales', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByArticleDates

	public function getFilterByComments()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();

		$sales = Sale::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Ventas que contienen <strong>'. $input['comments'] .'</strong> en el comentario del remisionero.';

		return View::make('sales.index')
				->with(compact('title', 'sales', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByComments

	public function getFilterByArticleComments()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();
		$idsSale = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->saleItems as $sitem) {
				$idsSale .= $sitem->sale->id .',';
				$amounts[$sitem->sale->id] = $sitem->amount;
			}

		}

		$idsSale = trim($idsSale, ',');

		$sales = Sale::whereRaw('id in ('. $idsSale .')')
			->whereRaw('comments like "%'. $input['comments'] .'%"')
			->orderBy('id', 'desc')->paginate(50);

		$mensaje = 'Ventas que contienen el artículo <strong>'. $articleName .'</strong> y en el comentario del remisionero <strong>'. $input['comments'] .'</strong>.';

		return View::make('sales.list')
				->with(compact('title', 'sales', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByCommentsArticle

	public function getFilterByStatusArticleDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();
		$idsSale = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->saleItems as $sitem) {
				$idsSale .= $sitem->sale->id .',';
				$amounts[$sitem->sale->id] = $sitem->amount;
			}

		}

		$idsSale = trim($idsSale, ',');

		$sales = Sale::whereRaw('id in ('. $idsSale .')')
			->whereRaw('status = "'. $input['estado'] .'" AND (created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'")')
			->orderBy('id', 'asc')->paginate(50);

		$mensaje = 'Ventas con estado <strong>'. $input['estado'] .'</strong> entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';

		return View::make('sales.list')
				->with(compact('title', 'sales', 'mensaje', 'input', 'amounts', 'mensaje', 'TIPO_REMISION'));

	} #getFilterByArticleDates

} #SaleController
