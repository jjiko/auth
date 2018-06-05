<?php
Route::group(['namespace' => 'Jiko\Auth\Http\Controllers'], function () {
  Route::prefix('auth')->group(function () {

    Route::name('auth.register_path')->get('register', 'AuthController@getRegister');
    Route::name('auth.login_path')->get('login', 'AuthController@getLogin');
    Route::name('auth.logout_path')->get('logout', 'AuthController@getLogout');

    // user account providers
    Route::name('auth.redirect')->get('redirect/{provider}', 'AuthController@redirectToProvider');
    Route::name('auth.handler')->get('handler/{provider}', 'AuthController@handleProviderCallback');

    // oauth connections ie. twitter, nest
    Route::name('auth.connect.redirect')->get('connection/redirect/{provider}', 'AuthController@redirectToProvider');
    Route::name('auth.connect.handler')->any('connection/handler/{provider}', 'AuthController@handleConnectionCallback');
  });
  Route::prefix('user')->group(function () {
    Route::name('auth.user_info')->get('/', 'AuthController@getUser');
  });
});