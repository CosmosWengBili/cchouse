<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {
    Route::post('selectize', 'FeatureController@selectize');
    Route::post('shareHolders', 'FeatureController@shareHolders');
});

Route::group(['middleware' => ['api', 'cors']], function () {
    Route::post('/bank/webhook', 'API\ReceivableController@incoming');
    Route::post('/bank/ubot/webhook', 'API\ReceivableController@ubotWebhook');
});
