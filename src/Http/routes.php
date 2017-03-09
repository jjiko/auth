<?php
Route::group(['namespace' => 'Jiko\Auth\Http\Controllers'], function () {
  Route::get('/auth/register', ['as' => 'auth.register_path', 'uses' => 'AuthController@getRegister']);
  Route::get('/auth/login', ['as' => 'auth.login_path', 'uses' => 'AuthController@getLogin']);
  Route::get('/auth/logout', ['as' => 'auth.logout_path', 'uses' => function () {
    Auth::logout();
    return redirect('/');
  }]);
  Route::get('/auth/redirect/{provider}', ['as' => 'auth.redirect', 'uses' => 'AuthController@redirectToProvider']);
  Route::get('/auth/handler/{provider}', ['as' => 'auth.handler', 'uses' => 'AuthController@handleProviderCallback']);

  Route::get('/user', ['as' => 'auth.user_info', 'uses' => 'AuthController@getUser']);
  Route::get('once', function(){
//    $user = \Jiko\Auth\User::where('email', '=', 'joejiko@gmail.com')->first();
//    $user->attachRole(1);
    $perm = new \Jiko\Auth\Permission();
    $perm->name = 'create-page';
    $perm->display_name = 'Create Page';
    $perm->description = 'Create new pages on site.';
    $perm->save();

    $editUser = new \Jiko\Auth\Permission();
    $editUser->name = 'modify-user';
    $editUser->display_name = 'Modify Users';
    $editUser->description = 'modify existing users';
    $editUser->save();

    $admin = \Jiko\Auth\Role::find(1);
    $admin->attachPermission([$perm, $editUser]);

  });
});