<?php

class DamageStore extends Eloquent {

    protected $table = 'damage_stores';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function damage()
    {
        return $this->belongsTo('Damage');
    }

}