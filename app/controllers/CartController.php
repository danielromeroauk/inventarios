<?php

class CartController extends BaseController {

    public function getIndex()
    {
        $title = 'Carrito';

        return View::make('carts.index')
            ->with(compact('title'));
    }

    public function postAdd()
    {
        $input = Input::all();
        $title = 'Agregar a carrito';

        $article = Article::find($input['id']);
        $cantidad = $input['cantidad'];
        $cart = array();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        $cart[$article->id] = array($article, $cantidad);

        Session::put('cart', $cart);

        return View::make('carts.index')
            ->with(compact('title'));

    }

    public function getClear()
    {
        $title = 'Vaciar carrito';
        Session::forget('cart');

        return View::make('carts.index')
            ->with(compact('title'));
    }

}