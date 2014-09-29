<?php

class PurchaseController extends BaseController {

	const TIPO_REMISION = 'purchases';

	public function getIndex()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$purchases = Purchase::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compras con estado <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>.';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'TIPO_REMISION'));

	} #getIndex

	public function postAdd()
	{
		$title = 'Proceso de compra';
		$cart = array();
		$input = Input::all();

		if (Session::has('cart'))
		{
			$cart = Session::get('cart');
		}

		if (empty($cart))
		{
			return Redirect::to('purchases');
		}

		//Crea la compra y guarda la referencia a ella.
		$purchase = self::saveInPurchaseTable();

		foreach ($cart as $item)
		{
			self::saveInPurchaseItemTable($item['article']->id, $item['amount'], $purchase->id);

		} #foreach $cart as $item

		Session::forget('cart');

		return Redirect::to('purchases/items/'. $purchase->id);

	} #postAdd

	private function saveInPurchaseTable()
	{
		try
		{
			$input = Input::all();
			$purchaseTable = new Purchase();

			$purchaseTable->user_id = Auth::user()->id;
			$purchaseTable->branch_id = $input['branch_id'];
			$purchaseTable->comments = $input['comments'];
			$purchaseTable->status = 'pendiente';

			$purchaseTable->save();

			return $purchaseTable;

		}
		catch (Exception $e)
		{
			die('No se pudo guardar el registro en compras.');
		}

	} #saveInPurchaseTable

	private function saveInPurchaseItemTable($idArticle, $amount, $idPurchase)
	{
		try
		{
			$purchaseItemsTable = new PurchaseItem();
			$purchaseItemsTable->purchase_id = $idPurchase;
			$purchaseItemsTable->article_id = $idArticle;
			$purchaseItemsTable->amount = $amount;
			$purchaseItemsTable->save();
		}
		catch (Exception $e)
		{
			die('No se pudo guardar el artículo '. $idArticle .' como item de la compra.');
		}

	} #saveInPurchaseItemTable

	private function saveInStockTable($idBranch, $idArticle, $amount)
	{
		try
		{
			$articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

			if(!empty($articleStock))
			{
				$articleStock->stock += $amount;
				$articleStock->save();
			}
			else
			{
				$stockTable = new Stock();
				$stockTable->branch_id = $idBranch;
				$stockTable->article_id = $idArticle;
				$stockTable->stock = $amount;
				$stockTable->minstock = 0;
				$stockTable->save();

			} #if !empty($ArticleStock)

		}
		catch (Exception $e)
		{
			die('No se pudo modificar el stock del artículo'. $idArticle .' en la sucursal '. $idBranch);
		}

	} #saveInStockTable

	public function getItems($idPurchase)
	{
		$title = 'Items de compra';
		$purchase = Purchase::find($idPurchase);
		$pitems = PurchaseItem::where('purchase_id', '=', $idPurchase)->get();

		return View::make('purchases.items')
			->with(compact('title', 'purchase', 'pitems'));

	} #getItems

	public function postPurchaseStore()
	{
		try
		{
			$input = Input::all();
			$purchase = Purchase::find($input['purchase']);

			// Si no existe notaparcial y está pendiente es porque se está finalizando la remisión.
			if(!isset($input['notaparcial']) && $purchase->status == 'pendiente')
			{
				$pitems = PurchaseItem::where('purchase_id', '=', $input['purchase'])->get();

				foreach ($pitems as $pitem)
				{
					self::saveInStockTable($input['branch_id'], $pitem->article->id, $pitem->amount);
				}

				/*Cambiar el status en la tabla purchase a finalizado*/
				$purchase = Purchase::find($input['purchase']);
				$purchase->status = 'finalizado';
				$purchase->save();
			}

			$purchaseStore = new PurchaseStore();
			$purchaseStore->purchase_id = $input['purchase'];
			$purchaseStore->user_id = Auth::user()->id;
			$purchaseStore->comments = $input['comments'];
			$purchaseStore->save();

			return Redirect::to('purchases/items/'. $input['purchase']);

		}
		catch (Exception $e)
		{
			die('No se pudo aumentar el stock.<br />');
		}

	} #postPurchaseStore

	public function getCancel($idPurchase)
	{
		try
		{
			$purchase = Purchase::find($idPurchase);

			if ($purchase->status != 'finalizado')
			{
				$purchase->status = 'cancelado';
				$purchase->save();
			}

			return Redirect::to('purchases/items/'. $idPurchase);

		}
		catch (Exception $e)
		{
			die('No fue posible cancelar la compra.');
		}

	} #getCancel

	public function getFilterByStatus()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();

		$purchases = Purchase::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compras con estado <strong>'. $input['estado'] .'</strong>';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByStatus

	public function getFilterByStatusBranch()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();

		$branch = Branche::find($input['branch_id']);

		$purchases = Purchase::where('status', '=', $input['estado'])->where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compras con estado <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByStatusBranch

	public function getFilterById()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compra';
		$input = Input::all();

		$purchases = Purchase::where('id', '=', $input['idRemision'])->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compra con código '. $input['idRemision'] .'</strong>';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterById

	public function getFilterByArticle()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();
		$idsPurchase = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->purchaseItems as $pitem) {
				$idsPurchase .= $pitem->purchase->id .',';
				$amounts[$pitem->purchase->id] = $pitem->amount;
			}

		}

		$idsPurchase = trim($idsPurchase, ',');

		$purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')->orderBy('id', 'desc')->paginate(50);

		$mensaje = 'Compras que contienen el artículo <strong>'. $articleName .'</strong>';

		return View::make('purchases.list')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByArticle

	public function getFilterByDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();

		$purchases = Purchase::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compras con fecha de creación entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong>';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByDates

	public function getFilterByArticleDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();
		$idsPurchase = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->purchaseItems as $pitem) {
				$idsPurchase .= $pitem->purchase->id .',';
				$amounts[$pitem->purchase->id] = $pitem->amount;
			}

		}

		$idsPurchase = trim($idsPurchase, ',');

		$purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')
			->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
			->orderBy('id', 'asc')->paginate(50);

		$mensaje = 'Compras entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';


		return View::make('purchases.list')
			->with(compact('title', 'purchases', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByArticleDates

	public function getFilterByComments()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();

		$purchases = Purchase::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(6);

		$mensaje = 'Compras que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

		return View::make('purchases.index')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'TIPO_REMISION'));

	} #getFilterByComments

	public function getFilterByArticleComments()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Compras';
		$input = Input::all();
		$idsPurchase = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->purchaseItems as $pitem) {
				$idsPurchase .= $pitem->purchase->id .',';
				$amounts[$pitem->purchase->id] = $pitem->amount;
			}

		}

		$idsPurchase = trim($idsPurchase, ',');

		$purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')
			->whereRaw('comments like "%'. $input['comments'] .'%"')
			->orderBy('id', 'desc')->paginate(50);

		$mensaje = 'Compras que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

		return View::make('purchases.list')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'amounts', 'TIPO_REMISION'));

	} #getFilterByArticleComments

	public function getFilterByStatusArticleDates()
	{
		$TIPO_REMISION = self::TIPO_REMISION;
		$title = 'Ventas';
		$input = Input::all();
		$idsPurchase = '0';
		$amounts = array();
		$articleName = $input['article'];

		$article = Article::find($input['article']);

		if (!empty($article)) {
			$articleName = $article->name;

			foreach ($article->purchaseItems as $pitem) {
				$idsPurchase .= $pitem->purchase->id .',';
				$amounts[$pitem->purchase->id] = $pitem->amount;
			}

		}

		$idsPurchase = trim($idsPurchase, ',');

		$purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')
			->whereRaw('status = "'. $input['estado'] .'" AND (created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'")')
			->orderBy('id', 'asc')->paginate(50);

		$mensaje = 'Compras con estado <strong>'. $input['estado'] .'</strong> entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';

		return View::make('purchases.list')
				->with(compact('title', 'purchases', 'mensaje', 'input', 'amounts', 'mensaje', 'TIPO_REMISION'));

	} #getFilterByArticleDates


} #PurchaseController
