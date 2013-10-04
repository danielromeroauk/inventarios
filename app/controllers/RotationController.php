<?php

class RotationController extends BaseController {

    public function getIndex()
    {
        $title = 'Rotaciones';
        $rotations = Rotation::orderBy('id', 'desc')->paginate(5);

        return View::make('rotations.index')
                ->with(compact('title', 'rotations'));
    }

    public function postAdd()
    {
        $title = 'Proceso de rotación';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        foreach ($cart as $item) {
            $check = self::checkStock($item['article']->id, $input['branch_from'], $item['amount']);

            /*Comprueba si hay suficiente stock en la sucursal*/
            if ($check != 'Ok') {
                Session::flash('message', $check);

                return Redirect::to('cart');
            }
        }

        /*Crea el registro en la tabla rotations*/
        self::saveInRotationTable();

        /*Crea los registros en la tabla rotation_items*/
        foreach ($cart as $item) {
            self::saveInRotationItemTable($item['article']->id, $item['amount']);
        } #foreach $cart as $item

        /*Vacía el carrito*/
        Session::forget('cart');

        return Redirect::to('rotations');

    } #postRotation

    private function checkStock($idArticle, $idBranch, $amount)
    {
        $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();

        $articulo = Article::find($idArticle);
        $sucursal = Branche::find($idBranch);

        if (empty($articleStock) || $articleStock->stock < $amount) {
            return 'El stock del artículo <strong>'. $articulo->name .'</strong> en la sucursal <strong>'. $sucursal->name .'</strong> es insuficiente para realizar la rotación.';
        } else {
            return 'Ok';
        }
    }

    private function saveInRotationTable()
    {
        try {

            $input = Input::all();
            $rotationTable = new Rotation();

            $rotation['user_id'] = Auth::user()->id;
            $rotation['branch_from'] = $input['branch_from'];
            $rotation['branch_to'] = $input['branch_to'];
            $rotation['comments'] = $input['comments'];
            $rotation['status'] = 'pendiente en origen';

            $rotationTable->create($rotation);

        } catch (Exception $e) {
            die('No se pudo guardar el registro en rotaciones.');
        }

    } #saveInRotationTable

    private function saveInRotationItemTable($idArticle, $amount)
    {
        try {

            $rotation_id = Rotation::first()->orderBy('created_at', 'desc')->first()->id;

            $rotationItemsTable = new RotationItem(); /* rit */

            $rit['rotation_id'] = $rotation_id;
            $rit['article_id'] = $idArticle;
            $rit['amount'] = $amount;

            $rotationItemsTable->create($rit);

        } catch (Exception $e) {
            die($e .'No se pudo guardar el articulo '. $idArticle .' como item de la rotación.');
        }

    } #saveInRotationItemTable

    private function saveInStockTable($disminuir, $idBranch, $idArticle, $amount)
    {
        try {

            // $articleStock = Stock::whereRaw("article_id='". $idArticle ."' and branch_id='". $idBranch ."'")->first();
            // $articleStock = Stock::where('article_id', '=', $idArticle)->where('branch_id', '=', $idBranch)->first();
            $articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

            if(!empty($articleStock)) {
                if ($disminuir == true) {
                    $articleStock->stock -= $amount;
                } else {
                    $articleStock->stock += $amount;
                }
                $articleStock->update();
            } #if !empty($ArticleStock)

        } catch (Exception $e) {
            die('No se pudo modificar el stock del artículo'. $idArticle .' en la sucursal '. $idBranch);
        }
    }

    public function getItems($idRotation)
    {
        $title = 'Items de la rotación';
        $rotation = Rotation::find($idRotation);
        $ritems = RotationItem::where('rotation_id', '=', $idRotation)->get();

        return View::make('rotations.items')
            ->with(compact('title', 'rotation', 'ritems'));
    }

    public function postRotationStore()
    {
        $input = Input::all();
        try {

            $rotations = RotationItem::where('rotation_id', '=', $input['rotation'])->get();
            $rs['user_id'] = Auth::user()->id;

            foreach ($rotations as $rotation) {
                if (isset($input['branch_from'])) {
                    $rs['comments_from'] = $input['comments_from'];
                    self::saveInStockTable(true, $input['branch_from'], $rotation->article->id, $rotation->amount);
                } elseif (isset($input['branch_to'])) {
                    self::saveInStockTable(false, $input['branch_to'], $rotation->article->id, $rotation->amount);
                }
            } #foreach

            $rotationStore = RotationStore::find($input['rotation']);

            if (empty($rotationStore)) {
                $rotationStore = new RotationStore();
                $rs['id'] = $input['rotation'];
                $rotationStore->create($rs);
            } else {
                $rotationStore->comments_to = $input['comments_to'];
                $rotationStore->update();
            }

            /*Cambiar el status en la tabla rotation a finalizado*/
            $rotation = Rotation::find($input['rotation']);
            if (isset($input['branch_from'])) {
                $rotation->status = 'pendiente en destino';
            } elseif (isset($input['branch_to'])) {
                $rotation->status = 'finalizado';
            }
            $rotation->update();

            return Redirect::to('rotations');

        } catch (Exception $e) {

            Session::flash('message', 'Ha ocurrido un error, verifique si ya se han guardado los datos.');

            return Redirect::to('rotations/items/'. $input['rotation']);
        }
    } #postRotationStore

    public function getCancel($idRotation)
    {
        try {

            $rotation = Rotation::find($idRotation);
            $rotation->status = 'cancelado';
            $rotation->update();

            return Redirect::to('rotations');

        } catch (Exception $e) {
            die('No fue posible cancelar la rotación.');
        }
    }

} #RotationController