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
    return view('welcome');
});
Route::group(['namespace' => 'Xiaofei','prefix' => 'xf'], function () {
	Route::any('index.html', 'XiaofeiController@index_view')->name('index'); 
	Route::any('index_data', 'XiaofeiController@index_data')->name('index'); 
	Route::any('addDisoplay', 'XiaofeiController@addDisoplay')->name('index'); 
	Route::any('ajaxaddxf', 'XiaofeiController@ajaxaddxf')->name('index'); 
	Route::any('constat.html', 'XiaofeiController@constat')->name('index'); 
	
});




//分组--中间件
Route::group(['middleware' => ['JwtAuto'],'namespace' => 'Test','prefix' => 'pc'], function() {
    Route::any('home.html','TestController@home');
});

