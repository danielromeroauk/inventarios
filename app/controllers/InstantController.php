<?php

class InstantController extends BaseController {

    public function getIndex()
    {
        $title = 'Entregas inmediatas';
        $instants = Instant::orderBy('id', 'desc')->paginate(5);

        return View::make('instants.index')
                ->with(compact('title', 'instants'));
    }

    public function postAdd()
    {
        $title = 'Proceso de entrega inmediata';
        $cart = array();
        $input = Input::all();
        $idBranch = Auth::user()->roles()->first()->branch->id;

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        foreach ($cart as $item) {
            $check = self::checkStock($item['article']->id, $idBranch, $item['amount']);

            /*Comprueba si hay suficiente stock en la sucursal*/
            if ($check != 'Ok') {
                Session::flash('message', $check);

                return Redirect::to('cart');
            }
        }

        /*Crea el registro en la tabla instants*/
        self::saveInInstantTable();

        /*Crea los registros en la tabla instant_items*/
        foreach ($cart as $item) {
            self::saveInInstantItemTable($item['article']->id, $item['amount']);
        } #foreach $cart as $item

        /*Disminuye el campo stock en la tabla stocks*/
        $instant_id = Instant::first()->orderBy('created_at', 'desc')->first()->id;
        self::saveInInstantStore($instant_id);

        /*Vacía el carrito*/
        Session::forget('cart');

        return Redirect::to('instants');

    } #postInstant

    private function checkStock($idArticle, $idBranch, $amount)
    {
        $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();

        $articulo = Article::find($idArticle);
        $sucursal = Branche::find($idBranch);

        if (empty($articleStock) || $articleStock->stock < $amount) {
            return 'El stock del artículo <strong>'. $articulo->name .'</strong> en la sucursal <strong>'. $sucursal->name .'</strong> es insuficiente para realizar la entrega inmediata.';
        } else {
            return 'Ok';
        }
    }

    private function saveInInstantTable()
    {
        try {

            $input = Input::all();
            $instantTable = new Instant();

            $instant['user_id'] = Auth::user()->id;
            $instant['branch_id'] = Auth::user()->roles()->first()->branch->id;
            $instant['comments'] = $input['comments'];

            $instantTable->create($instant);

        } catch (Exception $e) {
            die('No se pudo guardar el registro en entregas inmediatas.');
        }

    } #saveInInstantTable

    private function saveInInstantItemTable($idArticle, $amount)
    {
        try {

            $instant_id = Instant::first()->orderBy('created_at', 'desc')->first()->id;

            $instantItemsTable = new InstantItem(); /* iit */

            $iit['instant_id'] = $instant_id;
            $iit['article_id'] = $idArticle;
            $iit['amount'] = $amount;

            $instantItemsTable->create($iit);

        } catch (Exception $e) {
            die('No se pudo guardar el articulo '. $idArticle .' como item de la entrega inmediata.');
        }

    } #saveInInstantItemTable

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

    public function getItems($idInstant)
    {
        $title = 'Items de la entrega inmediata';
        $instant = Instant::find($idInstant);
        $iitems = InstantItem::where('instant_id', '=', $idInstant)->get();

        return View::make('instants.items')
            ->with(compact('title', 'instant', 'iitems'));
    }

    private function saveInInstantStore($idInstant)
    {
        try {

            $input = Input::all();

            $idBranch = Auth::user()->roles()->first()->branch->id;

            $iitems = InstantItem::where('instant_id', '=', $idInstant)->get();

            foreach ($iitems as $item) {
                self::saveInStockTable($idBranch, $item->article->id, $item->amount);
            } #foreach

            $instantStore = new InstantStore();
            $is['id'] = $idInstant;
            $is['user_id'] = Auth::user()->id;
            $is['comments'] = 'Entrega inmediata exitosa.';
            $instantStore->create($is);

            return Redirect::to('instants');

        } catch (Exception $e) {
            die('No se pudo disminuir el stock.');
        }
    } #postInstantStore

    public function getCancel($idInstant)
    {
        try {

            $instant = Instant::find($idInstant);
            $instant->status = 'cancelado';
            $instant->update();

            return Redirect::to('instants');

        } catch (Exception $e) {
            die('No fue posible cancelar la venta.');
        }
    }

} #InstantController