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

    public static $medidas = array(
        'Unidades' => 'Unidades',
        'Pares' => 'Pares',
        'Kilogramos' => 'Kilogramos',
        'Metros' => 'Metros',
        'Centímetros' => 'Centímetros',
				'Bultos' => 'Bultos'
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
     * Devuelve un arreglo con todos los movimientos de un artículo ordenados por fecha ascendente.
     * Mezcla compras, ventas, daños y entregas inmediatas.
     * Se excluyen las rotaciones porque se asume que todos los establecimientos están bajo el mismo nit.
     * Se creó esta función para tener un log del artículo sin importar la sucursal.
     */
    public function movimientos($fecha1, $fecha2)
    {
        //Arreglo que tendrá todos los movimientos
        $movimientos = [];


        //Inicio con las compras
        $ids = '0';
        $amounts = array();

        //Obtiene las compras que tienen el artículo actual dentro de sus items
        foreach ($this->purchaseItems as $item)
        {
            $ids .= $item->purchase->id .',';
            $amounts[$item->purchase->id] = $item->amount;
        }

        //Convierte el string en un array
        $ids = trim($ids, ',');

        //Obtiene las compras del artículo actual
        $purchases = Purchase::whereRaw('id in ('. $ids .')')
                ->whereRaw('created_at BETWEEN "'. $fecha1 .'" AND "'. $fecha2 .'"')
                ->orderBy('created_at', 'asc')
                ->get();

        foreach ($purchases as $purchase)
        {
            $movimientos[] = array(
                'fecha' => $purchase->created_at,
                'tipo' => 'compra',
                'id' => $purchase->id,
                'sucursal' => $purchase->branch()->first()->name,
                'cantidad' => $amounts[$purchase->id],
                'estado' => $purchase->status,
                'comentario' => $purchase->comments,
                'nota' => (isset($purchase->purchaseStore()->orderBy('created_at', 'desc')->first()->comments)) ? $purchase->purchaseStore()->orderBy('created_at', 'desc')->first()->comments : ''
            );
        }


        //Inicio con las ventas
        $ids = '0';
        $amounts = array();

        //Obtengo las ventas que tienen el artículo actual dentro de sus items
        foreach ($this->saleItems as $item)
        {
            $ids .= $item->sale->id .',';
            $amounts[$item->sale->id] = $item->amount;
        }

        //Convierte el string en un array
        $ids = trim($ids, ',');

        //Obtiene las ventas del artículo actual
        $sales = Sale::whereRaw('id in ('. $ids .')')
                ->whereRaw('created_at BETWEEN "'. $fecha1 .'" AND "'. $fecha2 .'"')
                ->orderBy('created_at', 'asc')
                ->get();

        foreach ($sales as $sale)
        {
            $movimientos[] = array(
                'fecha' => $sale->created_at,
                'tipo' => 'venta',
                'id' => $sale->id,
                'sucursal' => $sale->branch()->first()->name,
                'cantidad' => $amounts[$sale->id],
                'estado' => $sale->status,
                'comentario' => $sale->comments,
                'nota' => (isset($sale->saleStore()->orderBy('created_at', 'desc')->first()->comments)) ? $sale->saleStore()->orderBy('created_at', 'desc')->first()->comments : ''
            );
        }


        //Inicio con los daños
        $ids = '0';
        $amounts = array();

        //Obtengo los daños que tienen el artículo actual dentro de sus items
        foreach ($this->damageItems as $item)
        {
            $ids .= $item->damage->id .',';
            $amounts[$item->damage->id] = $item->amount;
        }

        //Convierte el string en un array
        $ids = trim($ids, ',');

        //Obtiene los daños del artículo actual
        $damages = Damage::whereRaw('id in ('. $ids .')')
                ->whereRaw('created_at BETWEEN "'. $fecha1 .'" AND "'. $fecha2 .'"')
                ->orderBy('created_at', 'asc')
                ->get();

        foreach ($damages as $damage)
        {
            $movimientos[] = array(
                'fecha' => $damage->created_at,
                'tipo' => 'daño',
                'id' => $damage->id,
                'sucursal' => $damage->branch()->first()->name,
                'cantidad' => $amounts[$damage->id],
                'estado' => $damage->status,
                'comentario' => $damage->comments,
                'nota' => (isset($damage->damageStore()->orderBy('created_at', 'desc')->first()->comments)) ? $damage->damageStore()->orderBy('created_at', 'desc')->first()->comments : ''
            );
        }


        //Inicio con las entregas inmediatas
        $ids = '0';
        $amounts = array();

        //Obtengo las entregas inmediatas que tienen el artículo actual dentro de sus items
        foreach ($this->instantItems as $item)
        {
            $ids .= $item->instant->id .',';
            $amounts[$item->instant->id] = $item->amount;
        }

        //Convierte el string en un array
        $ids = trim($ids, ',');

        //Obtiene las entregas inmediatas del artículo actual
        $instants = Instant::whereRaw('id in ('. $ids .')')
                ->whereRaw('created_at BETWEEN "'. $fecha1 .'" AND "'. $fecha2 .'"')
                ->orderBy('created_at', 'asc')
                ->get();

        foreach ($instants as $instant)
        {
            $movimientos[] = array(
                'fecha' => $instant->created_at,
                'tipo' => 'entrega inmediata',
                'id' => $instant->id,
                'sucursal' => $instant->branch()->first()->name,
                'cantidad' => $amounts[$instant->id],
                'estado' => $instant->status,
                'comentario' => $instant->comments,
                'nota' => (isset($instant->instantStore()->orderBy('created_at', 'desc')->first()->comments)) ? $instant->instantStore()->orderBy('created_at', 'desc')->first()->comments : ''
            );
        }

        //Ordena el arreglo por el primer elemento de los arreglos internos fecha
        asort($movimientos);

        return $movimientos;

    } #movimientos

    public function ventaReciente()
    {
        $reciente = SaleItem::where('article_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if(!empty($reciente))
        {
            return $reciente->created_at;
        }
        else
        {
            return '2013-01-01';
        }


    } #ventaReciente

} #Article
