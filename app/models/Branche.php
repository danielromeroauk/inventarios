<?php

class Branche extends Eloquent {

    protected $table = 'branches';

    protected $guarded = array();

    public static $rules = array(
        'name' => 'required'
    );

    public static $messages = array(
        'name.required' => 'El nombre es requerido.'
    );

    public function roles()
    {
        return $this->hasMany('Role');
    }

    public function stocks()
    {
        return $this->hasMany('Stock');
    }

}