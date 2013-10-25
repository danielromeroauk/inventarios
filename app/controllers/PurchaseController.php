<?php

class PurchaseController extends BaseController {

    public function getIndex()
    {
        $title = 'Compras';
        $purchases = Purchase::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con estado = <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>';

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

    } #postPurchase

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
            die('No se pudo guardar el articulo '. $idArticle .' como item de la compra.');
        }

    } #saveInPurchaseItemTable

    private function saveInStockTable($idBranch, $idArticle, $amount)
    {
        try {

            // $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();
            // $articleStock = Stock::where('article_id', '=', $idArticle)->where('branch_id', '=', $idBranch)->first();
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
    }

    public function getItems($idPurchase)
    {
        $title = 'Items de compra';
        $purchase = Purchase::find($idPurchase);
        $pitems = PurchaseItem::where('purchase_id', '=', $idPurchase)->get();

        return View::make('purchases.items')
            ->with(compact('title', 'purchase', 'pitems'));
    }

    public function postPurchaseStore()
    {
        try {

            $input = Input::all();

            $pitems = PurchaseItem::where('purchase_id', '=', $input['purchase'])->get();

            foreach ($pitems as $pitem) {
                self::saveInStockTable($input['branch_id'], $pitem->article->id, $pitem->amount);
            } #foreach

            $purchaseStore = new PurchaseStore();
            $ps['id'] = $input['purchase'];
            $ps['user_id'] = Auth::user()->id;
            $ps['comments'] = $input['comments'];
            $purchaseStore->create($ps);

            /*Cambiar el status en la tabla purchase a finalizado*/
            $purchase = Purchase::find($input['purchase']);
            $purchase->status = 'finalizado';
            $purchase->update();

            return Redirect::to('purchases');

        } catch (Exception $e) {
            die('No se pudo aumentar el stock.');
        }
    } #postPurchaseStore

    public function getCancel($idPurchase)
    {
        try {

            $purchase = Purchase::find($idPurchase);
            $purchase->status = 'cancelado';
            $purchase->update();

            return Redirect::to('purchases');

        } catch (Exception $e) {
            die('No fue posible cancelar la compra.');
        }
    }

    public function postFilterByStatus()
    {
        $title = 'Compras';

        $purchases = Purchase::where('status', '=', Input::get('estado'))->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con <strong>estado = '. Input::get('estado') .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByStatus

    public function postFilterByStatusBranch()
    {
        $title = 'Compras';

        $branch = Branche::find(Input::get('branch_id'));

        $purchases = Purchase::where('status', '=', Input::get('estado'))->where('branch_id', '=', Input::get('branch_id'))->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con estado = <strong>'. Input::get('estado') .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByStatusBranch

    public function postFilterById()
    {
        $title = 'Compras';

        $purchases = Purchase::where('id', '=', Input::get('idPurchase'))->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con <strong>código de compra = '. Input::get('idPurchase') .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterById

    public function postFilterByArticle()
    {
        $title = 'Compras';
        $idsPurchase = '0';

        $articles = Article::whereRaw("id = '". Input::get('article') ."' OR name like '%". Input::get('article') ."%'")->get();

        foreach ($articles as $article) {
            foreach ($article->purchaseItems as $pitems) {
                $idsPurchase += $pitems->purchase->id .',';
            }
        }

        $idsPurchase = trim($idsPurchase, ',');

        $purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con <strong>código o parte del nombre del articulo = '. Input::get('article') .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByArticle

    public function postFilterByDates()
    {
        $title = 'Compras';

        $purchases = Purchase::whereRaw('created_at BETWEEN "'. Input::get('fecha1') .'" AND "'. Input::get('fecha2') .'"')->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con <strong>fecha entre '. Input::get('fecha1') .' y '. Input::get('fecha2') .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByDates

    public function postFilterByArticleDates()
    {
        $title = 'Compras';
        $idsPurchase = '0';

        $articles = Article::whereRaw("id = '". Input::get('article') ."' OR name like '%". Input::get('article') ."%'")->get();

        foreach ($articles as $article) {
            foreach ($article->purchaseItems as $pitems) {
                $idsPurchase += $pitems->purchase->id .',';
            }
        }

        $idsPurchase = trim($idsPurchase, ',');

        $purchases = Purchase::whereRaw('id in ('. $idsPurchase .')')
            ->whereRaw('created_at BETWEEN "'. Input::get('fecha1') .'" AND "'. Input::get('fecha2') .'"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con código o parte del nombre del articulo = <strong>'. Input::get('article') .'</strong> en las compras realizadas entre '. Input::get('fecha1') .' y '. Input::get('fecha2');

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByArticleDates

     public function postFilterByComments()
    {
        $title = 'Compras';

        $purchases = Purchase::whereRaw("comments like '%". Input::get('comments') ."%'")->orderBy('id', 'desc')->paginate(5);

        $filterPurchase = 'Resultados con comentarios que contienen <strong>'. Input::get('comments') .'</strong>';

        return View::make('purchases.index')
                ->with(compact('title', 'purchases', 'filterPurchase'));
    } #postFilterByComments

} #PurchaseController
