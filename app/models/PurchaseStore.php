<?php

class PurchaseStore extends Eloquent {

    protected $table = 'purchase_stores';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function purchase()
    {
        return $this->belongsTo('Purchase');
    }

}