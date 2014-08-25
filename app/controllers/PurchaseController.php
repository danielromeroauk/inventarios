<?php

class PurchaseController extends BaseController {

    public function getIndex()
    {
        $title = 'Compras';
        $purchases = Purchase::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras con estado <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>.';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));

    } #getIndex

    public function postAdd()
    {
        $title = 'Proceso de compra';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        if (empty($cart)) {
            return Redirect::to('purchases');
        }

        self::saveInPurchaseTable();

        foreach ($cart as $item) {
            self::saveInPurchaseItemTable($item['article']->id, $item['amount']);
        } #foreach $cart as $item

        Session::forget('cart');

        return Redirect::to('purchases');

    } #postAdd

    private function saveInPurchaseTable()
    {
        try {

            $input = Input::all();
            $purchaseTable = new Purchase();

            $purchase['user_id'] = Auth::user()->id;
            $purchase['branch_id'] = $input['branch_id'];
            $purchase['comments'] = $input['comments'];
            $purchase['status'] = 'pendiente';

            $purchaseTable->create($purchase);

        } catch (Exception $e) {
            die('No se pudo guardar el registro en compras.');
        }

    } #saveInPurchaseTable

    private function saveInPurchaseItemTable($idArticle, $amount)
    {
        try {

            $purchase_id = Purchase::first()->orderBy('created_at', 'desc')->first()->id;

            $purchaseItemsTable = new PurchaseItem(); /* pit */

            $pit['purchase_id'] = $purchase_id;
            $pit['article_id'] = $idArticle;
            $pit['amount'] = $amount;

            $purchaseItemsTable->create($pit);

        } catch (Exception $e) {
            die('No se pudo guardar el artículo '. $idArticle .' como item de la compra.');
        }

    } #saveInPurchaseItemTable

    private function saveInStockTable($idBranch, $idArticle, $amount)
    {
        try {

            $articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

            if(!empty($articleStock)) {
                $articleStock->stock += $amount;
                $articleStock->update();
            } else {
                $StockTable = new Stock();

                $stock['branch_id'] = $idBranch;
                $stock['article_id'] = $idArticle;
                $stock['stock'] = $amount;
                $stock['minstock'] = 0;

                $StockTable->create($stock);
            } #if !empty($ArticleStock)

        } catch (Exception $e) {
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
        try {

            $input = Input::all();

            /*Verifica que la remisión de verdad está activa*/
            $purchase = Purchase::find($input['purchase']);
            if (in_array($purchase->status, array('finalizado', 'cancelado'))) {
                return Redirect::to('purchases/items/'. $input['purchase']);
            }

            if($input['notaparcial'] == 'false')
            {
                $pitems = PurchaseItem::where('purchase_id', '=', $input['purchase'])->get();

                foreach ($pitems as $pitem) {
                    self::saveInStockTable($input['branch_id'], $pitem->article->id, $pitem->amount);
                } #foreach

                /*Cambiar el status en la tabla purchase a finalizado*/
                $purchase = Purchase::find($input['purchase']);
                $purchase->status = 'finalizado';
                $purchase->update();
            } #if notaparcial == 'false'

            $purchaseStore = new PurchaseStore();
            $ps['purchase_id'] = $input['purchase'];
            $ps['user_id'] = Auth::user()->id;
            $ps['comments'] = $input['comments'];
            $purchaseStore->create($ps);

            return Redirect::to('purchases/items/'. $input['purchase']);

        } catch (Exception $e) {
            die('No se pudo aumentar el stock.<br />');
        }

    } #postPurchaseStore

    public function getCancel($idPurchase)
    {
        try {

            $purchase = Purchase::find($idPurchase);

            if ($purchase->status != 'finalizado') {

                $purchase->status = 'cancelado';
                $purchase->update();

            }

            return Redirect::to('purchases/items/'. $idPurchase);

        } catch (Exception $e) {
            die('No fue posible cancelar la compra.');
        }

    } #getCancel

    public function getFilterByStatus()
    {
        $title = 'Compras';
        $input = Input::all();

        $purchases = Purchase::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras con estado <strong>'. $input['estado'] .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByStatus

    public function getFilterByStatusBranch()
    {
        $title = 'Compras';
        $input = Input::all();

        $branch = Branche::find($input['branch_id']);

        $purchases = Purchase::where('status', '=', $input['estado'])->where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras con estado <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByStatusBranch

    public function getFilterById()
    {
        $title = 'Compra';
        $input = Input::all();

        $purchases = Purchase::where('id', '=', $input['idPurchase'])->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compra con código '. $input['idPurchase'] .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterById

    public function getFilterByArticle()
    {
        $title = 'Compras';
        $input = Input::all();
        $idsPurchase = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->purchaseItems as $pitems) {
                $idsPurchase .= $pitems->purchase->id .',';
            }

        }

        $idsPurchase = trim($idsPurchase, ',');

        $purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras que contienen el artículo <strong>'. $input['article'] .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByArticle

    public function getFilterByDates()
    {
        $title = 'Compras';
        $input = Input::all();

        $purchases = Purchase::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras con fecha de creación entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByDates

    public function getFilterByArticleDates()
    {
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
            ->orderBy('id', 'asc')->paginate(100);

        $filterPurchase = 'Compras entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';


        return View::make('purchases.list')
            ->with(compact('title', 'purchases', 'filterPurchase', 'input', 'amounts'));

    } #getFilterByArticleDates

    public function getFilterByComments()
    {
        $title = 'Compras';
        $input = Input::all();

        $purchases = Purchase::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByComments

    public function getFilterByArticleComments()
    {
        $title = 'Compras';
        $input = Input::all();
        $idsPurchase = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->purchaseItems as $pitems) {
                $idsPurchase .= $pitems->purchase->id .',';
            }

        }

        $idsPurchase = trim($idsPurchase, ',');

        $purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')
            ->whereRaw('comments like "%'. $input['comments'] .'%"')
            ->orderBy('id', 'desc')->paginate(6);

        $filterPurchase = 'Compras que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase', 'input'));

    } #getFilterByArticleComments


} #PurchaseController
