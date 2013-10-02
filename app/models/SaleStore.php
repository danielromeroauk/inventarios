<?php

class SaleStore extends Eloquent {

    protected $table = 'sale_stores';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function sale()
    {
        return $this->belongsTo('Sale');
    }

}