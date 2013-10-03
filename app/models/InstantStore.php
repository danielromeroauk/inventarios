<?php

class InstantStore extends Eloquent {

    protected $table = 'instant_stores';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function instant()
    {
        return $this->belongsTo('Instant');
    }

}