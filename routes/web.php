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
    return redirect('/home');
});

Route::group(['middleware' => 'auth','prefix'=>'home'], function () {
  Route::group(['prefix'=>'order'],function()  {
    Route::get('hotel',function(){
      return view('claim.hotel');
    });
  });



  Route::group(['prefix'=>'claim'],function()  {
    Route::group(['prefix'=>'detail'],function(){
      Route::get('/{id}','ClaimController@show');
    });
    Route::group(['prefix'=>'delete'],function(){
      Route::get('/{id}','ClaimController@destroy');
    });
    Route::group(['prefix'=>'reject'],function(){
      Route::get('/{id}','ClaimController@reject');
    });
    Route::get('/list/{status}','ClaimController@index');
    Route::get('/re_order/{id}','OrderController@rebookHotel');
  });

  Route::group(['prefix'=>'approve'],function(){
    Route::get('/{id}','ApproverController@approve');
  });

  Route::group(['prefix'=>'reject'],function(){
    Route::get('/{id}','ApproverController@reject');
  });
  Route::group(['prefix'=>'approver'],function()  {
    Route::group(['prefix'=>'detail'],function(){

      Route::get('/{id}','ClaimController@show');
    });
//  Todo: masang fungsi approve dan reject
//  Route::group(['prefix'=>'reject'],function(){
//    Route::get('/{id}','ClaimController@destroy');
//  });


    Route::get('/received','ApproverController@showReceived');
    Route::get('/approved','ApproverController@showApproved');
    Route::get('/rejected','ApproverController@showRejected');

  });

  Route::group(['prefix'=>'finance'],function()  {
    Route::get('/buy/{id}','OrderController@orderHotel');
    Route::get('/received','FinanceController@showReceived');
    Route::get('/approved','FinanceController@showApproved');
    Route::get('/rejected','FinanceController@showRejected');
  });




    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Route::group(['prefix'=>'api'],function()  {
  Route::get('/',function()  {
    return "200";
  });
  Route::post('/token','OrderController@getToken');
  Route::post('/hotel/list','OrderController@getHotel');
  Route::post('/hotel/detail','OrderController@getHotelDetail');
  Route::post('/book/hotel','OrderController@bookHotel');
  Route::post('/claim/detil','OrderController@getOrder');
});
