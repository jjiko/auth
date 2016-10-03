<?php
Route::group(['namespace' => 'Jiko\Auth'], function () {
  Route::get('/auth/redirect/{provider}', ['as' => $s . 'redirect', 'uses' => 'AuthController@getAuthRedirect']);
  Route::get('/auth/handler/{provider}', ['as' => $s . 'handler', 'uses' => 'AuthController@getAuthHandler']);
});