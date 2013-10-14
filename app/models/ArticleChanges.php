<?php

class ArticleChange extends Eloquent {

    protected $table = 'article_changes';

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