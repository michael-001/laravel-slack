<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get("/", function(){
    return response()->json("Welcome to Laravel Slack");
});


Route::group(["prefix"=>"auth"], function(){
   Route::post('register', 'Auth\AuthController@register');
   Route::post('login', 'Auth\AuthController@login');
});


Route::group(['middleware'=>'auth:api'], function(){
   Route::post('auth/logout', 'Auth\AuthController@logout');
   Route::get('me', function(){
      return response()->json(auth('api')->user());
   });

    Route::apiResource('user', 'UserController')
    ->only([
        'show', 'update'
    ]);

    Route::apiResource('channel', 'ChannelController');
    Route::apiResource('channel.message', 'Message\MessageController');


});

