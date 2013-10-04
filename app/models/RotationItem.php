<?php

class RotationItem extends Eloquent {

    protected $table = 'rotation_items';

    protected $guarded = array();

    public static $rules = array();

    public function rotation()
    {
        return $this->belongsTo('Rotation');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}