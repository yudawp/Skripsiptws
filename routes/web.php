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
Route::middleware(['auth'])->group(function () {
	Route::get('/', function () {
	    return view('welcome');
	});
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/allfile', 'HomeController@allfile');
Route::get('/addfile', 'HomeController@addfile');
Route::post('/uploadfile', 'HomeController@uploadfile');
Route::get('/files/{file}', 'HomeController@download');
Route::get('/unduh/{id}', 'HomeController@unduh');
Route::post('/actunduh', 'HomeController@actUnduh');
