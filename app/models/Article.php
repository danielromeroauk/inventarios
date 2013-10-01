<?php

class Article extends Eloquent {

    protected $table = 'articles';

    protected $guarded = array();

    public static $rules = array(
        'name' => 'required',
        'unit' => 'required',
        'price' => 'required',
        'iva' => 'required',
    );

    public static $messages = array(
        'name.required' => 'El nombre es requerido.',
        'unit.required' => 'La unidad de medida es requerida.',
        'price.required' => 'El precio es requerido.',
        'iva.required' => 'El IVA es requerido.'
    );

    public function stocks()
    {
        return $this->hasMany('Stock');
    }

}