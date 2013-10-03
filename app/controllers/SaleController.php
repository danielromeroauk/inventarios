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

        foreach ($cart as $item) {
            $check = self::checkStock($item['article']->id, $input['branch_id'], $item['amount']);

            /*Comprueba si hay suficiente stock en la sucursal*/
            if ($check != 'Ok') {
                Session::flash('message', $check);

                return Redirect::to('cart');
            }
        }

        /*Crea el registro en la tabla sales*/
        self::saveInSaleTable();

        /*Crea los registros en la tabla sale_items*/
        foreach ($cart as $item) {
            self::saveInSaleItemTable($item['article']->id, $item['amount']);
        } #foreach $cart as $item

        /*Vacía el carrito*/
        Session::forget('cart');

        return Redirect::to('sales');

    } #postSale

    private function checkStock($idArticle, $idBranch, $amount)
    {
        $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();

        $articulo = Article::find($idArticle);
        $sucursal = Branche::find($idBranch);

        if (empty($articleStock) || $articleStock->stock < $amount) {
            return 'El stock del artículo <strong>'. $articulo->name .'</strong> en la sucursal <strong>'. $sucursal->name .'</strong> es insuficiente para realizar la venta.';
        } else {
            return 'Ok';
        }
    }

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

            $saleItemsTable = new SaleItem(); /* sit */

            $sit['sale_id'] = $sale_id;
            $sit['article_id'] = $idArticle;
            $sit['amount'] = $amount;

            $saleItemsTable->create($sit);

        } catch (Exception $e) {
            die($e .'No se pudo guardar el articulo '. $idArticle .' como item de la venta.');
        }

    } #saveInSaleItemTable

    private function saveInStockTable($idBranch, $idArticle, $amount)
    {
        try {

            // $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();
            // $articleStock = Stock::where('article_id', '=', $idArticle)->where('branch_id', '=', $idBranch)->first();
            $articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

            if(!empty($articleStock)) {
                $articleStock->stock -= $amount;
                $articleStock->update();
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
        try {

            $input = Input::all();

            $sales = SaleItem::where('sale_id', '=', $input['sale'])->get();

            foreach ($sales as $sale) {
                self::saveInStockTable($input['branch_id'], $sale->article->id, $sale->amount);
            } #foreach

            $saleStore = new SaleStore();
            $ss['id'] = $input['sale'];
            $ss['user_id'] = Auth::user()->id;
            $ss['comments'] = $input['comments'];
            $saleStore->create($ss);

            /*Cambiar el status en la tabla sale a finalizado*/
            $sale = Sale::find($input['sale']);
            $sale->status = 'finalizado';
            $sale->update();

            return Redirect::to('sales');

        } catch (Exception $e) {
            die('No se pudo disminuir el stock.');
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