<?php

class InstantItem extends Eloquent {

    protected $table = 'instant_items';

    protected $guarded = array();

    public static $rules = array();

    public function instant()
    {
        return $this->belongsTo('Instant');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}