<?php

class Stock extends Eloquent {

    protected $table = 'stocks';

    protected $guarded = array();

    public static $rules = array();

    public function branch()
    {
        return $this->belongsTo('Branche');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}