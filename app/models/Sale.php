<?php

class Sale extends Eloquent {

    protected $table = 'sales';

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

    public function saleItems()
    {
        return $this->hasMany('SaleItem');
    }

    public function saleStore()
    {
        return $this->hasMany('SaleStore');
    }

}