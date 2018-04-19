<?php
Route::group(['namespace' => 'Jiko\Auth\Http\Controllers'], function () {
  Route::name('auth.register_path')->get('/auth/register', 'AuthController@getRegister');
  Route::name('auth.login_path')->get('/auth/login', 'AuthController@getLogin');
  Route::name('auth.logout_path')->get('/auth/logout', 'AuthController@getLogout');
  Route::name('auth.redirect')->get('/auth/redirect/{provider}', 'AuthController@redirectToProvider');
  Route::name('auth.connect.redirect')->get('/auth/connection/redirect/{provider}', 'AuthController@redirectToProvider');
  Route::name('auth.connect.handler')->any('/auth/connection/handler/{provider}', 'AuthController@handleConnectionCallback');
  Route::name('auth.handler')->get('/auth/handler/{provider}', 'AuthController@handleProviderCallback');

  Route::name('auth.user_info')->get('/user', 'AuthController@getUser');
  Route::get('once', 'AuthController@getOnce');
});