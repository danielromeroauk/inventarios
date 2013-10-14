<?php

class PurchaseController extends BaseController {

    public function getIndex()
    {
        $title = 'Compras';
        $purchases = Purchase::orderBy('id', 'desc')->paginate(5);

        return View::make('purchases.index')
                ->with(compact('title', 'purchases'));
    }

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

} #PurchaseController