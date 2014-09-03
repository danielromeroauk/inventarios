<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    $title = 'Inicio';

    return View::make('inicio')
    ->with('title', $title);
});

Route::get('login', 'UserController@getIndex');
Route::get('logout', 'UserController@getLogout');
Route::post('users/index', 'UserController@postIndex');


Route::group(array('before' => 'auth'), function()
{
    Route::controller('users', 'UserController');
    Route::controller('articles', 'ArticleController');
    Route::controller('branches', 'BranchController');
    Route::controller('cart', 'CartController');
    Route::controller('purchases', 'PurchaseController');
    Route::controller('sales', 'SaleController');
    Route::controller('damages', 'DamageController');
    Route::controller('instants', 'InstantController');
    Route::controller('rotations', 'RotationController');
});
