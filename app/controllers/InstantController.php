<?php

class InstantController extends BaseController {

    public function getIndex()
    {
        $title = 'Entregas inmediatas';
        $instants = Instant::where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas de la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>.';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant'));

    } #getIndex

    public function postAdd()
    {
        $title = 'Proceso de entrega inmediata';
        $cart = array();
        $input = Input::all();
        $idBranch = Auth::user()->roles()->first()->branch->id;

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        if (empty($cart)) {
            return Redirect::to('instants');
        }

        foreach ($cart as $item) {

            $check = Article::checkStock($item['article'], Auth::user()->roles()->first()->branch->id, $item['amount'], 'entrega inmediata');

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
            die('No se pudo guardar el artículo '. $idArticle .' como item de la entrega inmediata.');
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

            /*Verifica que la remisión de verdad está activa*/
            $instant = Instant::find($idInstant);
            if (in_array($instant->status, array('finalizado', 'cancelado'))) {
                return Redirect::to('instants/items/'. $idInstant);
            }

            $idBranch = Auth::user()->roles()->first()->branch->id;

            $iitems = InstantItem::where('instant_id', '=', $idInstant)->get();

            foreach ($iitems as $item) {
                self::saveInStockTable($idBranch, $item->article->id, $item->amount);
            } #foreach

            $instantStore = new InstantStore();
            $is['id'] = $idInstant;
            $is['user_id'] = Auth::user()->id;
            $is['comments'] = 'Ok.';
            $instantStore->create($is);

            return Redirect::to('instants/items/'. $idInstant);

        } catch (Exception $e) {
            die('No se pudo disminuir el stock.'. $e);
        }
    } #saveInInstantStore

    public function getCancel($idInstant)
    {
        try {

            $instant = Instant::find($idInstant);

            if ($instant->status != 'finalizado') {

                $instant->status = 'cancelado';
                $instant->update();

            }

            return Redirect::to('instants/items/'. $idInstant);

        } catch (Exception $e) {
            die('No fue posible cancelar la entrega inmediata.');
        }
    } #getCancel

    public function getFilterByBranch()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();

        $branch = Branche::find($input['branch_id']);

        $instants = Instant::where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas de la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterByStatusBranch

    public function getFilterById()
    {
        $title = 'Entrega inmediata';
        $input = Input::all();

        $instants = Instant::where('id', '=', $input['idInstant'])->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entrega inmediata con código '. $input['idInstant'] .'</strong>';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterById

    public function getFilterByArticle()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();
        $idsInstant = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->instantItems as $iitems) {
                $idsInstant .= $iitems->instant->id .',';
            }

        }

        $idsInstant = trim($idsInstant, ',');

        $instants = Instant::whereRaw('id in ('. $idsInstant .')')->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas que contienen el artículo <strong>'. $input['article'] .'</strong>';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterByArticle

    public function getFilterByDates()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();

        $instants = Instant::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas con fecha de creación entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong>';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterByDates

    public function getFilterByArticleDates()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();
        $idsInstant = '0';
        $amounts = array();
        $articleName = $input['article'];

        $article = Article::find($input['article']);

        if (!empty($article)) {
            $articleName = $article->name;

            foreach ($article->instantItems as $iitem) {
                $idsInstant .= $iitem->instant->id .',';
                $amounts[$iitem->instant->id] = $iitem->amount;
            }

        }

        $idsInstant = trim($idsInstant, ',');

        $instants = Instant::whereRaw('id in ('. $idsInstant .')')
            ->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
            ->orderBy('id', 'asc')->paginate(100);

        $filterInstant = 'Entregas inmediatas entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';

        return View::make('instants.list')
                ->with(compact('title', 'instants', 'filterInstant', 'input', 'amounts'));

    } #getFilterByArticleDates

    public function getFilterByComments()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();

        $instants = Instant::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterByComments

    public function getFilterByArticleComments()
    {
        $title = 'Entregas inmediatas';
        $input = Input::all();
        $idsInstant = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->instantItems as $iitems) {
                $idsInstant .= $iitems->instant->id .',';
            }

        }

        $idsInstant = trim($idsInstant, ',');

        $instants = Instant::whereRaw('id in ('. $idsInstant .')')
            ->whereRaw('comments like "%'. $input['comments'] .'%"')
            ->orderBy('id', 'desc')->paginate(6);

        $filterInstant = 'Entregas inmediatas que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

        return View::make('instants.index')
                ->with(compact('title', 'instants', 'filterInstant', 'input'));

    } #getFilterByArticleComments

} #InstantController
