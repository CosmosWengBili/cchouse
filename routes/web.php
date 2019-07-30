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

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('index');
    });
});

Auth::routes();

// should be in auth
Route::group(['middleware' => ['with.nested']], function () {
    Route::resource('buildings', 'BuildingController');
    Route::resource('rooms', 'RoomController');
    Route::resource('keys', 'KeyController');
    Route::resource('keyRequests', 'KeyRequestController');
});
