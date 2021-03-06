<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('https://engagenie.com');
});
Route::get('app', 'IndexController@index');
Route::get('cookie-preload', 'IndexController@cookie_preload');

Route::get('config', 'SchoolController@show');