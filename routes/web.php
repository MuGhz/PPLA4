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
  return view('construction');
});

Route::group(['middleware' => 'auth','prefix'=>'home'], function () {
  Route::group(['prefix'=>'order'],function()  {
    Route::get('hotel',function() {
      return view('claim.hotel');
    });
    Route::get('plane',function() {
      return view('claim.pesawat');
    });
  });



  Route::group(['prefix'=>'claim'],function()  {
    Route::group(['prefix'=>'detail'],function(){
      Route::get('/{id}','ClaimController@show');
    });
    Route::group(['prefix'=>'delete'],function(){
      Route::get('/{id}','ClaimController@destroy');
    });
    Route::get('/list/{status}','ClaimController@index');
  });

  Route::group(['prefix'=>'approver'],function()  {
	Route::get('/received','ApproverController@showReceived');
	Route::get('/approved','ApproverController@showApproved');
	Route::get('/rejected','ApproverController@showRejected');

  });


    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Route::group(['prefix'=>'api'],function()  {
  Route::post('/token','OrderController@getToken');
  Route::post('/hotel/list','OrderController@getHotel');
  Route::post('/plane/list','OrderController@getPlane');
  Route::post('/hotel/detail','OrderController@getHotelDetail');
  Route::post('/plane/detail','OrderController@getPlaneDetail');
  Route::post('/book/hotel','OrderController@bookHotel');
  Route::post('/book/plane','OrderController@bookPlane');
  Route::post('/claim/detil','OrderController@getOrder');
});

Route::get('{any}',function(){
  return view('construction');
});
