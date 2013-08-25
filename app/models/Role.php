<?php

class Role extends Eloquent {

    protected $table = 'roles';

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

}