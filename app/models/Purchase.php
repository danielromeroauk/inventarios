<?php

class Purchase extends Eloquent {

    protected $table = 'purchases';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function branch()
    {
        return $this->belongsTo('Branche');
    }

    public function purchaseItems()
    {
        return $this->hasMany('PurchaseItem');
    }

    public function purchaseStore()
    {
        return $this->hasMany('PurchaseStore');
    }

}