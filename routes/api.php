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

Route::post('test', 'LogicTest@Result')->name('testLogic');
Route::group(['prefix' => 'carts'], function(){
    Route::post('{id}', 'CartController@AddItems');
    Route::put('{id}', 'CartController@UpdateItem');
    Route::delete('{id}', 'CartController@DeleteItem');
    Route::post('{id}/discount', 'CartController@AddDiscount');
    Route::get('{id}', 'CartController@GetCart');
});
