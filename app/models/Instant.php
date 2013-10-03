<?php

class Instant extends Eloquent {

    protected $table = 'instants';

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

    public function instantItems()
    {
        return $this->hasMany('InstantItem');
    }

    public function instantStore()
    {
        return $this->hasOne('InstantStore', 'id');
    }

}