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

Route::group(['middleware' => 'auth','prefix'=>'home'], function () {
  Route::group(['prefix'=>'order'],function()  {
    Route::get('hotel',function(){
      return view('claim.hotel');
    });
  });

  Route::group(['prefix'=>'approver'],function()  {
	Route::post('/received','ClaimController@index');
	Route::post('/approved','ClaimController@index');
	Route::post('/rejected','ClaimController@index');
  });

    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Route::group(['prefix'=>'api'],function()  {
  Route::post('/token','OrderController@getToken');
  Route::post('/hotel-list','OrderController@getHotel');
});
