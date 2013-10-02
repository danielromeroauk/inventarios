<?php

class SaleController extends BaseController {

    public function getIndex()
    {
        $title = 'Ventas';
        $sales = Sale::orderBy('id', 'desc')->paginate(5);

        return View::make('sales.index')
                ->with(compact('title', 'sales'));
    }

    public function postAdd()
    {
        $title = 'Proceso de venta';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        self::saveInSaleTable();

        foreach ($cart as $item) {
            self::saveInSaleItemTable($item[0]->id, $item[1]);
        } #foreach $cart as $item

        Session::forget('cart');

        return Redirect::to('sales');

    } #postSale

    private function saveInSaleTable()
    {
        try {

            $input = Input::all();
            $saleTable = new Sale();

            $sale['user_id'] = Auth::user()->id;
            $sale['branch_id'] = $input['branch_id'];
            $sale['comments'] = $input['comments'];
            $sale['status'] = 'pendiente';

            $saleTable->create($sale);

        } catch (Exception $e) {
            die('No se pudo guardar el registro en ventas.');
        }

    } #saveInSaleTable

    private function saveInSaleItemTable($idArticle, $amount)
    {
        try {

            $sale_id = Sale::first()->orderBy('created_at', 'desc')->first()->id;

            $saleItemsTable = new SaleItem(); /* pit */

            $pit['sale_id'] = $sale_id;
            $pit['article_id'] = $idArticle;
            $pit['amount'] = $amount;

            $saleItemsTable->create($pit);

        } catch (Exception $e) {
            die('No se pudo guardar el articulo '. $idArticle .' como item de la venta.');
        }

    } #saveInSaleItemTable

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

    public function getItems($idSale)
    {
        $title = 'Items de sale';
        $sale = Sale::find($idSale);
        $pitems = SaleItem::where('sale_id', '=', $idSale)->get();

        return View::make('sales.items')
            ->with(compact('title', 'sale', 'pitems'));
    }

    public function postSaleStore()
    {
        try {

            $input = Input::all();

            $pitems = SaleItem::where('sale_id', '=', $input['sale'])->get();

            foreach ($pitems as $pitem) {
                self::saveInStockTable($input['branch_id'], $pitem->article->id, $pitem->amount);
            } #foreach

            $saleStore = new SaleStore();
            $ps['id'] = $input['sale'];
            $ps['user_id'] = Auth::user()->id;
            $ps['comments'] = $input['comments'];
            $saleStore->create($ps);

            /*Cambiar el status en la tabla sale a finalizado*/
            $sale = Sale::find($input['sale']);
            $sale->status = 'finalizado';
            $sale->update();

            return Redirect::to('sales');

        } catch (Exception $e) {
            die('No se pudo aumentar el stock.');
        }
    } #postSaleStore

    public function getCancel($idSale)
    {
        try {

            $sale = Sale::find($idSale);
            $sale->status = 'cancelado';
            $sale->update();

            return Redirect::to('sales');

        } catch (Exception $e) {
            die('No fue posible cancelar la venta.');
        }
    }

} #SaleController