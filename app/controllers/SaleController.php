<?php

class SaleController extends BaseController {

    public function getIndex()
    {
        $title = 'Ventas';
        $sales = Sale::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas con estado = <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    }

    public function postAdd()
    {
        $title = 'Proceso de venta';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        if (empty($cart)) {
            return Redirect::to('sales');
        }

        foreach ($cart as $item) {

            $check = Article::checkStock($item['article'], $input['branch_id'], $item['amount'], 'venta');

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

    public function getFilterByStatus()
    {
        $title = 'Ventas';
        $input = Input::all();

        $sales = Sale::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas con estado = <strong>'. $input['estado'] .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByStatus

    public function getFilterByStatusBranch()
    {
        $title = 'Ventas';
        $input = Input::all();

        $branch = Branche::find($input['branch_id']);

        $sales = Sale::where('status', '=', $input['estado'])->where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas con estado = <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByStatusBranch

    public function getFilterById()
    {
        $title = 'Ventas';
        $input = Input::all();

        $sales = Sale::where('id', '=', $input['idSale'])->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Venta con código = <strong>'. $input['idSale'] .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterById

    public function getFilterByArticle()
    {
        $title = 'Ventas';
        $input = Input::all();
        $idsSale = '0';

        $article = Article::find($input['article']);

        foreach ($article->saleItems as $sitems) {
            $idsSale .= $sitems->sale->id .',';
        }

        $idsSale = trim($idsSale, ',');

        $sales = Sale::whereRaw('id in ('. $idsSale .')')->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas que contienen el artículo = <strong>'. $input['article'] .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByArticle

    public function getFilterByDates()
    {
        $title = 'Ventas';
        $input = Input::all();

        $sales = Sale::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas con fecha de creación entre <strong>'. Input::get('fecha1') .'</strong> y <strong>'. Input::get('fecha2') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByDates

    public function getFilterByArticleDates()
    {
        $title = 'Ventas';
        $input = Input::all();
        $idsSale = '0';

        $article = Article::find($input['article']);

        foreach ($article->saleItems as $sitems) {
            $idsSale .= $sitems->sale->id .',';
        }

        $idsSale = trim($idsSale, ',');

        $sales = Sale::whereRaw('id in ('. $idsSale .')')
            ->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el articulo <strong>'. $input['article'] .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByArticleDates

    public function getFilterByComments()
    {
        $title = 'Ventas';
        $input = Input::all();

        $sales = Sale::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByComments

    public function getFilterByArticleComments()
    {
        $title = 'Ventas';
        $input = Input::all();
        $idsSale = '0';

        $article = Article::find($input['article']);

        foreach ($article->saleItems as $sitems) {
            $idsSale .= $sitems->sale->id .',';
        }

        $idsSale = trim($idsSale, ',');

        $sales = Purchase::whereRaw('id in ('. $idsSale .')')
            ->whereRaw('comments like "%'. $input['comments'] .'%"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Ventas que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale', 'input'));

    } #getFilterByCommentsArticle

} #SaleController
