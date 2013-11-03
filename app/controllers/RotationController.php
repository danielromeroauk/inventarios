<?php

class RotationController extends BaseController {

    public function getIndex()
    {
        $title = 'Rotaciones';
        $branchUser = Auth::user()->roles()->first()->branch;

        $rotations = Rotation::where('status', '<>', 'finalizado')->whereRaw('branch_from = "'. $branchUser->id .'" OR branch_to = "'. $branchUser->id .'"')->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones <strong>sin finalizar</strong> de la sucursal <strong>'. $branchUser->name .'</strong>.';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation'));

    } #getIndex

    public function postAdd()
    {
        $title = 'Proceso de rotación';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        if (empty($cart)) {
            return Redirect::to('rotations');
        }

        foreach ($cart as $item) {

            $check = Article::checkStock($item['article'], $input['branch_from'], $item['amount'], 'rotación');

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

    } #postAdd

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

            $articleStock = Stock::where('article_id', $idArticle)->where('branch_id', $idBranch)->first();

            if(!empty($articleStock)) {
                if ($disminuir == true) {
                    $articleStock->stock -= $amount;
                } else {
                    $articleStock->stock += $amount;
                }
                $articleStock->update();
            } else {
                $StockTable = new Stock();

                $stock['branch_id'] = $idBranch;
                $stock['article_id'] = $idArticle;
                $stock['stock'] = $amount;
                $stock['minstock'] = 0;

                $StockTable->create($stock);
            } #if

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

    public function getFilterByStatus()
    {
        $title = 'Rotaciones';
        $input = Input::all();

        $rotations = Rotation::where('status', '=', $input['estado'])->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones con estado <strong>'. $input['estado'] .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByStatus

    public function getFilterByStatusBranch()
    {
        $title = 'Rotaciones';
        $input = Input::all();

        $branch = Branche::find($input['branch_id']);

        $rotations = Rotation::where('status', '=', $input['estado'])->whereRaw('branch_from = "'. $input['branch_id'] .'" OR branch_to = "'. $input['branch_id'] .'"')->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones con estado <strong>'. $input['estado'] .'</strong> en la sucursal <strong>'. $branch->name .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByStatusBranch

    public function getFilterById()
    {
        $title = 'Rotacion';
        $input = Input::all();

        $rotations = Rotation::where('id', '=', $input['idRotation'])->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotacion con código '. $input['idRotation'] .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterById

    public function getFilterByArticle()
    {
        $title = 'Rotaciones';
        $input = Input::all();
        $idsRotation = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->rotationItems as $ritems) {
                $idsRotation .= $ritems->rotation->id .',';
            }

        } #if


        $idsRotation = trim($idsRotation, ',');

        $rotations = Rotation::whereRaw('id in ('. $idsRotation .')')->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones que contienen el artículo <strong>'. $input['article'] .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByArticle

    public function getFilterByDates()
    {
        $title = 'Rotaciones';
        $input = Input::all();

        $rotations = Rotation::whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones con fecha de creación entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByDates

    public function getFilterByArticleDates()
    {
        $title = 'Rotaciones';
        $input = Input::all();
        $idsRotation = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->rotationItems as $ritems) {
                $idsRotation .= $ritems->rotation->id .',';
            }

        }

        $idsRotation = trim($idsRotation, ',');

        $rotations = Rotation::whereRaw('id in ('. $idsRotation .')')
            ->whereRaw('created_at BETWEEN "'. $input['fecha1'] .'" AND "'. $input['fecha2'] .'"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones entre <strong>'. $input['fecha1'] .'</strong> y <strong>'. $input['fecha2'] .'</strong> que contienen el artículo <strong>'. $input['article'] .'</strong>';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByArticleDates

    public function getFilterByComments()
    {
        $title = 'Rotaciones';
        $input = Input::all();

        $rotations = Rotation::whereRaw("comments like '%". $input['comments'] ."%'")->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones que contienen <strong>'. $input['comments'] .'</strong> en los comentarios del remisionero.';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByComments

    public function getFilterByArticleComments()
    {
        $title = 'Rotaciones';
        $input = Input::all();
        $idsRotation = '0';

        $article = Article::find($input['article']);

        if (!empty($article)) {

            foreach ($article->rotationItems as $ritems) {
                $idsRotation .= $ritems->rotation->id .',';
            }

        } #if

        $idsRotation = trim($idsRotation, ',');

        $rotations = Rotation::whereRaw('id in ('. $idsRotation .')')
            ->whereRaw('comments like "%'. $input['comments'] .'%"')
            ->orderBy('id', 'desc')->paginate(5);

        $filterRotation = 'Rotaciones que contienen el artículo <strong>'. $input['article'] .'</strong> y en los comentarios del remisionero <strong>'. $input['comments'] .'</strong>.';

        return View::make('rotations.index')
                ->with(compact('title', 'rotations', 'filterRotation', 'input'));

    } #getFilterByArticleComments

} #RotationController
