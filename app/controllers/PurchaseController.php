<?php

class PurchaseController extends BaseController {

	protected $rol;

	public function __construct(Purchase $rol)
	{
		$this->rol = $rol;
	}

    public function getIndex()
    {
        $title = 'Compras';
        $purchases = Purchase::all();

        return View::make('purchases.index')
                ->with(compact('title', 'purchases'));
    }

    public function postAdd()
    {
        $title = 'Proceso de compra';
        $cart = array();
        $input = Input::all();

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        }

        try {

            $purchaseTable = new Purchase();
            $purchase['user_id'] = Auth::user()->id;
            $purchase['branch_id'] = $input['branch_id'];
            $purchase['comments'] = $input['comments'];
            $purchase['status'] = 'pendiente';

            $purchaseTable->create($purchase);

            $purchase_id = Purchase::first()->orderBy('created_at', 'desc')->first()->id;
            $purchaseItemsTable = new PurchaseItem(); /* pit */

            foreach ($cart as $item) {
                $pit['purchase_id'] = $purchase_id;
                $pit['article_id'] = $item[0]->id;
                $pit['amount'] = $item[1];

                $purchaseItemsTable->create($pit);
            }

            Session::forget('cart');

            return Redirect::to('purchases');

        } catch (Exception $e) {
            /*No se puede guardar la compra en la tabla purchases y/o los items en la tabla purchase_items*/
            echo $e;
        }

    } #postPurchase

    public function getItems($idPurchase)
    {
        $title = 'Items de compra';
        $purchase = Purchase::find($idPurchase);
        $pitems = PurchaseItem::where('purchase_id', '=', $idPurchase)->get();

        return View::make('purchases.items')
            ->with(compact('title', 'purchase', 'pitems'));
    }

}