<?php

class ArticleImage extends Eloquent {

    protected $table = 'article_images';

    protected $guarded = array();

    public static $rules = array();

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function article()
    {
        return $this->belongsTo('Article');
    }

}