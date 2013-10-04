<?php

class RotationStore extends Eloquent {

    protected $table = 'rotation_stores';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function rotation()
    {
        return $this->belongsTo('Rotation');
    }

}