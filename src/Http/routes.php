<?php
Route::group(['namespace' => 'Jiko\Auth\Http\Controllers'], function () {
  Route::get('/auth', ['uses' => 'AuthController@getLogin']);
  Route::get('/auth/redirect/{provider}', ['as' => 'auth.redirect', 'uses' => 'AuthController@getAuthRedirect']);
  Route::get('/auth/handler/{provider}', ['as' => 'auth.handler', 'uses' => 'AuthController@getAuthHandler']);
});