<?php

class Damage extends Eloquent {

    protected $table = 'damages';

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

    public function damageItems()
    {
        return $this->hasMany('DamageItem');
    }

    public function damageStore()
    {
        return $this->hasMany('DamageStore');
    }

}