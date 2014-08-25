<?php

class DamageController extends BaseController {

    public function getIndex()
    {
        $title = 'Daños';
        $damages = Damage::where('status', '=', 'pendiente')->where('branch_id', '=', Auth::user()->roles()->first()->branch->id)->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños con estado <strong>pendiente</strong> en la sucursal <strong>'. Auth::user()->roles()->first()->branch->name .'</strong>.';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage'));

    } #getIndex

    public function postAdd()
    {
        $title = 'Proceso de daño';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        if (empty($cart)) {
            return Redirect::to('damages');
        }

        foreach ($cart as $item) {

            $check = Article::checkStock($item['article'], $input['branch_id'], $item['amount'], 'daño');

            /*Comprueba si hay suficiente stock en la sucursal*/
            if ($check != 'Ok') {
                Session::flash('message', $check);

                return Redirect::to('cart');
            }
        }

        /*Crea el registro en la tabla damages*/
        self::saveInDamageTable();

        /*Crea los registros en la tabla damage_items*/
        foreach ($cart as $item) {
            self::saveInDamageItemTable($item['article']->id, $item['amount']);
        } #foreach $cart as $item

        /*Vacía el carrito*/
        Session::forget('cart');

        return Redirect::to('damages');

    } #postDamage

    private function saveInDamageTable()
    {
        try {

            $input = Input::all();
            $damageTable = new Damage();

            $damage['user_id'] = Auth::user()->id;
            $damage['branch_id'] = $input['branch_id'];
            $damage['comments'] = $input['comments'];
            $damage['status'] = 'pendiente';

            $damageTable->create($damage);

        } catch (Exception $e) {
            die('No se pudo guardar el registro en daños.');
        }

    } #saveInDamageTable

    private function saveInDamageItemTable($idArticle, $amount)
    {
        try {

            $damage_id = Damage::first()->orderBy('created_at', 'desc')->first()->id;

            $damageItemsTable = new DamageItem(); /* dit */

            $dit['damage_id'] = $damage_id;
            $dit['article_id'] = $idArticle;
            $dit['amount'] = $amount;

            $damageItemsTable->create($dit);

        } catch (Exception $e) {
            die('No se pudo guardar el artículo '. $idArticle .' como item del daño.');
        }

    } #saveInDamageItemTable

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

    public function getItems($idDamage)
    {
        $title = 'Items del daño';
        $damage = Damage::find($idDamage);
        $ditems = DamageItem::where('damage_id', '=', $idDamage)->get();

        return View::make('damages.items')
            ->with(compact('title', 'damage', 'ditems'));
    }

    public function postDamageStore()
    {
        try {

            $input = Input::all();

            /*Verifica que la remisión de verdad está activa*/
            $damage = Damage::find($input['damage']);
            if (in_array($damage->status, array('finalizado', 'cancelado'))) {
                return Redirect::to('damages/items/'. $input['damage']);
            }

            if($input['notaparcial'] == 'false')
            {

                $damages = DamageItem::where('damage_id', '=', $input['damage'])->get();

                foreach ($damages as $damage) {
                    self::saveInStockTable($input['branch_id'], $damage->article->id, $damage->amount);
                } #foreach

                /*Cambiar el status en la tabla damage a finalizado*/
                $damage = Damage::find($input['damage']);
                $damage->status = 'finalizado';
                $damage->update();
            } #if notaparcial == 'false'

            $damageStore = new DamageStore();
            $ds['damage_id'] = $input['damage'];
            $ds['user_id'] = Auth::user()->id;
            $ds['comments'] = $input['comments'];
            $damageStore->create($ds);


            return Redirect::to('damages/items/'. $input['damage']);

        } catch (Exception $e) {
            die('No se pudo disminuir el stock.');
        }
    } #postDamageStore

    public function getCancel($idDamage)
    {
        try {

            $damage = Damage::find($idDamage);

            if ($damage->status != 'finalizado') {

                $damage->status = 'cancelado';
                $damage->update();

            }

            return Redirect::to('damages/items/'. $idDamage);

        } catch (Exception $e) {
            die('No fue posible cancelar el daño.');
        }
    }

    public function getFilterByStatus()
    {
        $title = 'Daños';
        $input = Input::all();

        $damages = Damage::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños con estado <strong>'. $input['estado'] .'</strong>';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByStatus

    public function getFilterByStatusBranch()
    {
        $title = 'Daños';
        $input = Input::all();

        $branch = Branche::find($input['branch_id']);

        $damages = Damage::where('status', '=', $input['estado'])->where('branch_id', '=', $input['branch_id'])->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños con estado <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByStatusBranch

    public function getFilterById()
    {
        $title = 'Daño';
        $input = Input::all();

        $damages = Damage::where('id', '=', $input['idDamage'])->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daño con código '. $input['idDamage'] .'</strong>';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterById

    public function getFilterByArticle()
    {
        $title = 'Daños';
        $input = Input::all();
        $idsDamage = '0';

        $article = Article::find($input['article']);

        foreach ($article->damageItems as $ditems) {
            $idsDamage .= $ditems->damage->id .',';
        }

        $idsDamage = trim($idsDamage, ',');

        $damages = Damage::whereRaw('id in ('. $idsDamage .')')->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños que contienen el artículo <strong>'. $input['article'] .'</strong>';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByArticle

    public function getFilterByDates()
    {
        $title = 'Daños';
        $input = Input::all();

        $damages = Damage::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños con fecha de creación entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong>';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByDates

    public function getFilterByArticleDates()
    {
        $title = 'Daños';
        $input = Input::all();
        $idsDamage = '0';
        $amounts = array();
        $articleName = $input['article'];

        $article = Article::find($input['article']);

        if (!empty($article)) {
            $articleName = $article->name;

            foreach ($article->damageItems as $ditem) {
                $idsDamage .= $ditem->damage->id .',';
                $amounts[$ditem->damage->id] = $ditem->amount;
            }

        }

        $idsDamage = trim($idsDamage, ',');

        $damages = Damage::whereRaw('id in ('. $idsDamage .')')
            ->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
            ->orderBy('id', 'asc')->paginate(100);

        $filterDamage = 'Daños entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $articleName .'</strong>';

        return View::make('damages.list')
                ->with(compact('title', 'damages', 'filterDamage', 'input', 'amounts'));

    } #getFilterByArticleDates

    public function getFilterByComments()
    {
        $title = 'Daños';
        $input = Input::all();

        $damages = Damage::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByComments

    public function getFilterByArticleComments()
    {
        $title = 'Daños';
        $input = Input::all();
        $idsDamage = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->damageItems as $ditems) {
                $idsDamage .= $ditems->damage->id .',';
            }

        }

        $idsDamage = trim($idsDamage, ',');

        $damages = Damage::whereRaw('id in ('. $idsDamage .')')
            ->whereRaw('comments like "%'. $input['comments'] .'%"')
            ->orderBy('id', 'desc')->paginate(6);

        $filterDamage = 'Daños que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

        return View::make('damages.index')
                ->with(compact('title', 'damages', 'filterDamage', 'input'));

    } #getFilterByArticleComments

} #DamageController
