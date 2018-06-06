<?php namespace Jiko\Auth\Http\Controllers;

use Jiko\Http\Controllers\Controller;

class AuthController extends Controller
{

  public function touch()
  {
    $user = request()->user();
    $user->touch();
  }

  public function getLogin()
  {
    $this->content('auth::login');
  }

  public function getLogout()
  {
    auth()->logout();
    return redirect('/');
  }

  public function getRegister()
  {
    return 'Register';
  }

  public function getUser()
  {
    $this->content('auth::user');
  }
}