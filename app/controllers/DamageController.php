<?php

class DamageController extends BaseController {

    public function getIndex()
    {
        $title = 'Daños';
        $damages = Damage::orderBy('id', 'desc')->paginate(5);

        return View::make('damages.index')
                ->with(compact('title', 'damages'));
    }

    public function postAdd()
    {
        $title = 'Proceso de daño';
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

    private function checkStock($idArticle, $idBranch, $amount)
    {
        $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();

        $articulo = Article::find($idArticle);
        $sucursal = Branche::find($idBranch);

        if (empty($articleStock) || $articleStock->stock < $amount) {
            return 'El stock del artículo <strong>'. $articulo->name .'</strong> en la sucursal <strong>'. $sucursal->name .'</strong> es insuficiente para registrar el daño.';
        } else {
            return 'Ok';
        }
    }

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
            die('No se pudo guardar el articulo '. $idArticle .' como item del daño.');
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

            $damages = DamageItem::where('damage_id', '=', $input['damage'])->get();

            foreach ($damages as $damage) {
                self::saveInStockTable($input['branch_id'], $damage->article->id, $damage->amount);
            } #foreach

            $damageStore = new DamageStore();
            $ds['id'] = $input['damage'];
            $ds['user_id'] = Auth::user()->id;
            $ds['comments'] = $input['comments'];
            $damageStore->create($ds);

            /*Cambiar el status en la tabla damage a finalizado*/
            $damage = Damage::find($input['damage']);
            $damage->status = 'finalizado';
            $damage->update();

            return Redirect::to('damages');

        } catch (Exception $e) {
            die('No se pudo disminuir el stock.');
        }
    } #postDamageStore

    public function getCancel($idDamage)
    {
        try {

            $damage = Damage::find($idDamage);
            $damage->status = 'cancelado';
            $damage->update();

            return Redirect::to('damages');

        } catch (Exception $e) {
            die('No fue posible cancelar el daño.');
        }
    }

} #DamageController