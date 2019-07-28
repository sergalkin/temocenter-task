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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::apiResource('news', 'NewsController')->parameters(['news' => 'id'])->middleware('news.api');

Route::group(['middleware' => ['api', 'news.api']], function () {
    Route::post('/news', 'NewsController@store')->name('news.store');
    Route::match(['put','patch'], '/news/{id}', 'NewsController@update')->name('news.update');
    Route::delete('/news/{id}', 'NewsController@destroy')->name('news.destroy');
});
Route::get('/news', 'NewsController@index')->name('news.index');
Route::get('/news/{id}', 'NewsController@show')->name('news.show');

Route::get('login', 'AuthController@login');
