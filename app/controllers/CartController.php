<?php

class CartController extends BaseController {

    public function getIndex()
    {
        $title = 'Carrito';
        $cart = array();

        if (!Session::has('cart')) {
            return Redirect::to('articles');
        }

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

        $cart[$article->id] = array('article' => $article, 'amount' => $cantidad);

        Session::put('cart', $cart);

        return View::make('carts.index')
            ->with(compact('title'));

    }

    public function getClear()
    {
        Session::forget('cart');

        return Redirect::to('cart');
    }

    public function getClearItem($idArticle)
    {
        $title = 'Quitar item';
        $cart = array();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        unset($cart[$idArticle]);

        Session::put('cart', $cart);

        return View::make('carts.index')
            ->with(compact('title'));

    }

}