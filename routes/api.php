<?php

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

Route::namespace('API')->group(function() {
    Route::post('user/add', 'UserController@store');

    Route::middleware('verify.api-key')->group(function() {
        Route::post('user/login', 'AuthController@login');
        Route::get('user/logout', 'AuthController@logout')->middleware('verify.user');
    });
});

Route::middleware(['verify.api-key', 'verify.user', 'check.permission'])->group(function() {
    Route::namespace('API')->group(function() {
        Route::post('user/update', 'UserController@update');
        Route::post('user/delete', 'UserController@destroy');
        Route::get('user/detail', 'UserController@show');
        Route::get('user/all', 'UserController@index');
    });
});
