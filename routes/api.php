<?php

use Illuminate\Support\Facades\Route;

// samo so localhost:8000/api treba da proverime dali raboti rutata
//Route::get('/', function () {return response()->json(['message' => 'Hello World'], 200);});


//Public routes
Route::get('me', 'App\Http\Controllers\User\MeController@getMe');


//Route group for authenticated users onlu
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');
    Route::put('settings/profile', 'App\Http\Controllers\User\SettingsController@updateProfile');
    Route::put('settings/password', 'App\Http\Controllers\User\SettingsController@updatePassword');
});

//Route group for guest users onlu
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'App\Http\Controllers\Auth\VerificationController@resend');
    Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset');
});
