<?php

class SaleItem extends Eloquent {

    protected $table = 'sale_items';

    protected $guarded = array();

    public static $rules = array();

    public function sale()
    {
        return $this->belongsTo('Sale');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}