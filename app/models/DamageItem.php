<?php

class DamageItem extends Eloquent {

    protected $table = 'damage_items';

    protected $guarded = array();

    public static $rules = array();

    public function damage()
    {
        return $this->belongsTo('Damage');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}