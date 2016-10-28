<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/search','SearchController@search');
Route::post('/search','SearchController@search');

Route::post('/create','CreatePLController@index');

Route::get('/create','CreatePLController@create');

Route::get('/api/search/','APIController@index');