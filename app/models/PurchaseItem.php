<?php

class PurchaseItem extends Eloquent {

    protected $table = 'purchase_items';

    protected $guarded = array();

    public static $rules = array();

    public function purchase()
    {
        return $this->belongsTo('Purchase');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}