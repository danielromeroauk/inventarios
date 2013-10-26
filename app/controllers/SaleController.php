<?php

class SaleController extends BaseController {

    public function getIndex()
    {
        $title = 'Ventas';
        $sales = Sale::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con estado = <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>';

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

    public function postFilterByStatus()
    {
        $title = 'Ventas';

        $sales = Sale::where('status', '=', Input::get('estado'))->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con estado = <strong>'. Input::get('estado') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByStatus

    public function postFilterByStatusBranch()
    {
        $title = 'Ventas';

        $branch = Branche::find(Input::get('branch_id'));

        $sales = Sale::where('status', '=', Input::get('estado'))->where('branch_id', '=', Input::get('branch_id'))->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con estado = <strong>'. Input::get('estado') .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByStatusBranch

    public function postFilterById()
    {
        $title = 'Ventas';

        $sales = Sale::where('id', '=', Input::get('idSale'))->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultado con código de venta = <strong>'. Input::get('idSale') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterById

    public function postFilterByArticle()
    {
        $title = 'Ventas';
        $idsSale = '0';

        $articles = Article::whereRaw("id = '". Input::get('article') ."' OR name like '%". Input::get('article') ."%'")->get();

        foreach ($articles as $article) {
            foreach ($article->saleItems as $sitems) {
                $idsSale += $sitems->sale->id .',';
            }
        }

        $idsSale = trim($idsSale, ',');

        $sales = Sale::whereRaw('id in ('. $idsSale .')')->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con código o parte del nombre del articulo = <strong>'. Input::get('article') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByArticle

    public function postFilterByDates()
    {
        $title = 'Ventas';

        $sales = Sale::whereRaw('created_at BETWEEN "'. Input::get('fecha1') .'" AND "'. Input::get('fecha2') .'"')->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con fecha entre <strong>'. Input::get('fecha1') .' y '. Input::get('fecha2') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByDates

    public function postFilterByArticleDates()
    {
        $title = 'Ventas';
        $idsSale = '0';

        $articles = Article::whereRaw("id = '". Input::get('article') ."' OR name like '%". Input::get('article') ."%'")->get();

        foreach ($articles as $article) {
            foreach ($article->saleItems as $sitems) {
                $idsSale += $sitems->sale->id .',';
            }
        }

        $idsSale = trim($idsSale, ',');

        $sales = Sale::whereRaw('id in ('. $idsSale .')')
            ->whereRaw('created_at BETWEEN "'. Input::get('fecha1') .'" AND "'. Input::get('fecha2') .'"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con código o parte del nombre del articulo = <strong>'. Input::get('article') .'</strong> en las compras realizadas entre '. Input::get('fecha1') .' y '. Input::get('fecha2');

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByArticleDates

    public function postFilterByComments()
    {
        $title = 'Ventas';

        $sales = Sale::whereRaw("comments like '%". Input::get('comments') ."%'")->orderBy('id', 'desc')->paginate(5);

        $filterSale = 'Resultados con comentarios que contienen <strong>'. Input::get('comments') .'</strong>';

        return View::make('sales.index')
                ->with(compact('title', 'sales', 'filterSale'));
    } #postFilterByComments

} #SaleController