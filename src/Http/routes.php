<?php
Route::namespace('Jiko\Auth\Http\Controllers')->middleware(['web'])->group(function () {
  Route::prefix('auth')->group(function () {

    Route::name('auth.register_path')->get('register', 'AuthController@getRegister');
    Route::name('auth.login_path')->get('login', 'AuthController@getLogin');
    Route::name('auth.logout_path')->get('logout', 'AuthController@getLogout');

    // user account providers
    Route::name('auth.redirect')->get('redirect/{provider}', 'AuthProviderController@redirect');
    Route::name('auth.handler')->get('handler/{provider}', 'AuthProviderController@callback');

    // oauth connections ie. twitter, nest
    Route::prefix('connection')->group(function(){
      Route::name('auth.connect.redirect')->get('redirect/{provider}', 'AuthConnectionController@redirect');
      Route::name('auth.connect.handler')->any('handler/{provider}', 'AuthConnectionController@callback');
    });
  });
  Route::prefix('user')->group(function () {
    Route::name('auth.user_info')->get('/', 'AuthController@getUser');
  });
});