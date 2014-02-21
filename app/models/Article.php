<?php

class Article extends Eloquent {

    protected $table = 'articles';

    protected $guarded = array();

    public static $rules = array(
        'name' => 'required',
        'unit' => 'required',
        'price' => 'required',
        'cost' => 'required',
        'iva' => 'required',
    );

    public static $messages = array(
        'name.required' => 'El nombre es requerido.',
        'unit.required' => 'La unidad de medida es requerida.',
        'price.required' => 'El precio es requerido.',
        'cost.required' => 'El costo es requerido.',
        'iva.required' => 'El IVA es requerido.'
    );

    public function stocks()
    {
        return $this->hasMany('Stock');
    }

    public function image()
    {
        return $this->hasOne('ArticleImage', 'id');
    }

    public function changes()
    {
        return $this->hasMany('ArticleChange', 'id');
    }

    public function purchaseItems()
    {
        return $this->hasMany('PurchaseItem');
    }

    public function saleItems()
    {
        return $this->hasMany('SaleItem');
    }

    public function damageItems()
    {
        return $this->hasMany('DamageItem');
    }

    public function instantItems()
    {
        return $this->hasMany('InstantItem');
    }

    public function rotationItems()
    {
        return $this->hasMany('RotationItem');
    }

    /**
     * Devuelve la cantidad involucrada en compras pendientes.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function inPurchases($branch)
    {
        $purchasesPendientes = Purchase::where('status', '=', 'pendiente')->where('branch_id', '=', $branch->id)->get();
        $cantidad = 0;

        foreach ($purchasesPendientes as $purchase) {

            foreach ($purchase->purchaseItems as $item) {
                if ($this->id == $item->article->id) {
                    $cantidad += $item->amount;
                }
            }

        }

        return $cantidad;
    }

    /**
     * Devuelve la cantidad involucrada en ventas pendientes.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function inSales($branch)
    {
        $salesPendientes = Sale::where('status', '=', 'pendiente')->where('branch_id', '=', $branch->id)->get();
        $cantidad = 0;

        foreach ($salesPendientes as $sale) {

            foreach ($sale->saleItems as $item) {
                if ($this->id == $item->article->id) {
                    $cantidad += $item->amount;
                }
            }

        }

        return $cantidad;
    }

    /**
     * Devuelve la cantidad involucrada en rotaciones pendientes en origen.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function inRotationsFrom($branch)
    {
        $rotationsPendientes = Rotation::where('status', '=', 'pendiente en origen')->where('branch_from', '=', $branch->id)->get();
        $cantidad = 0;

        foreach ($rotationsPendientes as $rotation) {

            foreach ($rotation->rotationItems as $item) {
                if ($this->id == $item->article->id) {
                    $cantidad += $item->amount;
                }
            }

        }

        return $cantidad;
    }

    /**
     * Devuelve la cantidad involucrada en rotaciones pendientes en destino.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function inRotationsTo($branch)
    {
        $rotationsPendientes = Rotation::where('status', '=', 'pendiente en destino')->where('branch_to', '=', $branch->id)->get();
        $cantidad = 0;

        foreach ($rotationsPendientes as $rotation) {

            foreach ($rotation->rotationItems as $item) {
                if ($this->id == $item->article->id) {
                    $cantidad += $item->amount;
                }
            }

        }

        return $cantidad;
    }

    /**
     * Devuelve la cantidad involucrada en daños pendientes.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function inDamages($branch)
    {
        $damagesPendientes = Damage::where('status', '=', 'pendiente')->where('branch_id', '=', $branch->id)->get();
        $cantidad = 0;

        foreach ($damagesPendientes as $damage) {

            foreach ($damage->damageItems as $item) {
                if ($this->id == $item->article->id) {
                    $cantidad += $item->amount;
                }
            }

        }

        return $cantidad;
    }

    /**
     * Devuelve la cantidad disponible para un nuevo movimiento de salida.
     * @param  Branche $branch
     * @return integer $cantidad
     */
    public function disponible($branch)
    {
        $cantidad = 0;

        foreach($this->stocks as $stock)
        {
            if($this->id == $stock->article->id && $branch->id == $stock->branch->id)
            {
                $cantidad = $stock->stock;
                break;
            }
        }

        $cantidad -= self::inSales($branch);
        $cantidad -= self::inRotationsFrom($branch);
        $cantidad -= self::inDamages($branch);

        return $cantidad;
    }

    /**
     * Devuelve Ok si hay stock suficiente para continuar con el tipo de movimiento.
     * De lo contrario retorna un mensaje indicando el artículo con stock insufiente en
     * la sucursal correspondiente.
     * @param  Article $Article
     * @param  string $idBranch   id de la sucursal.
     * @param  integer $amount    Cantidad a verificar contra stock.
     * @param  string $tipo       Tipo de movimiento que se está procesando: venta, entrega inmediata, daño ó rotación.
     * @return string             Mensaje de retorno.
     */
    public static function checkStock($article, $idBranch, $amount, $tipo)
    {
        $articleStock = Stock::whereRaw("article_id='". $article->id ."' and branch_id='". $idBranch ."'")->first();
        $branch = Branche::find($idBranch);

        if (empty($articleStock) || $article->disponible($branch) < $amount) {
            return 'El stock disponible del artículo <strong>'. $article->name .'</strong> en la sucursal <strong>'. $branch->name .'</strong> es insuficiente para <strong>'. $tipo .'</strong>.';
        } else {
            return 'Ok';
        }
    } #checkStock

    /**
     * Devuelve los movimientos realizados entre $fecha1 y $fecha2
     * @param  datetime $fecha1
     * @param  datetime $fecha2
     * @return array    $movimientos
     * array
     *   0
     *     array
     *       'id' => int 2
     *       'comments' => string 'Compra test.' (length=12)
     *       'status' => string 'finalizado' (length=10)
     *       'created_at' => string '2014-01-01 17:40:42' (length=19)
     *       'updated_at' => string '2014-01-01 18:26:32' (length=19)
    */
    public function movimientosEnFechas($fecha1='2014-01-01 00:00', $fecha2='2014-01-01 23:59')
    {
        $movimientos = array();

        $purchases = Purchase::where('created_at', '>=', $fecha1)
            ->where('created_at', '<=', $fecha2)
            ->orderBy('created_at', 'asc')->get();

        foreach ($purchases as $purchase) {
            foreach ($purchase->purchaseItems as $item) {
                if ($this->id == $item->article->id) {
                    $purchaseArray['created_at'] = $purchase->created_at;

                }
            }
        }

        $sales = Sale::where('created_at', '>=', $fecha1)
            ->where('created_at', '<=', $fecha2)
            ->orderBy('created_at', 'asc');

        $damages = Damage::where('created_at', '>=', $fecha1)
            ->where('created_at', '<=', $fecha2)
            ->orderBy('created_at', 'asc');

        $instants = Instant::where('created_at', '>=', $fecha1)
            ->where('created_at', '<=', $fecha2)
            ->orderBy('created_at', 'asc');

        $rotations = Rotation::where('created_at', '>=', $fecha1)
            ->where('created_at', '<=', $fecha2)
            ->orderBy('created_at', 'asc');

    } #movimientosEnFechas

}
