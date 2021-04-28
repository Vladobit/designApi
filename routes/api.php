<?php

use Illuminate\Support\Facades\Route;

// samo so localhost:8000/api treba da proverime dali raboti rutata
//Route::get('/', function () {return response()->json(['message' => 'Hello World'], 200);});


//Public routes
Route::get('me', 'App\Http\Controllers\User\MeController@getMe');

// Get Designs
Route::get('designs', 'App\Http\Controllers\Designs\DesignController@index');
Route::get('designs/{id}', 'App\Http\Controllers\Designs\DesignController@findDesign');



// Get Designs
Route::get('users', 'App\Http\Controllers\User\UserController@index');

//Route group for authenticated users onlu
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');
    Route::put('settings/profile', 'App\Http\Controllers\User\SettingsController@updateProfile');
    Route::put('settings/password', 'App\Http\Controllers\User\SettingsController@updatePassword');

    //Upload Images
    Route::post('designs', 'App\Http\Controllers\Designs\UploadController@upload');
    Route::put('designs/{id}', 'App\Http\Controllers\Designs\DesignController@update');
    Route::delete('designs/{id}', 'App\Http\Controllers\Designs\DesignController@destroy');

    //Comments
    Route::post('designs/{id}/comments', 'App\Http\Controllers\Designs\CommentController@store');
    Route::put('comments/{id}', 'App\Http\Controllers\Designs\CommentController@update');
    Route::delete('comments/{id}', 'App\Http\Controllers\Designs\CommentController@destroy');

    //Likes
    Route::post('designs/{id}/like', 'App\Http\Controllers\Designs\DesignController@like');
    Route::get('designs/{id}/liked', 'App\Http\Controllers\Designs\DesignController@checkIfUserHasLiked');
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
