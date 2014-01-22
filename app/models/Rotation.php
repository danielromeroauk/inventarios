<?php

class Rotation extends Eloquent {

    protected $table = 'rotations';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function branch_from()
    {
        return $this->belongsTo('Branche', 'branch_from');
    }

    public function branch_to()
    {
        return $this->belongsTo('Branche', 'branch_to');
    }

    public function rotationItems()
    {
        return $this->hasMany('RotationItem');
    }

    public function rotationStore()
    {
        return $this->hasMany('RotationStore');
    }

}