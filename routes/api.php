<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/auth', 'App\Http\Controllers\Api\Auth\AuthController@login');

    Route::middleware('auth:api')->group(function () {
        Route::post('/transactions', 'App\Http\Controllers\Api\V1\TransactionController@store');
    });
});
