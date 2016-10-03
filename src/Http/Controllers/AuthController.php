<?php namespace Jiko\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Jiko\Users\UserRepository;

class AuthController extends Controller {
  protected $auth;

  protected $userRepository;

  public function __construct(Guard $auth, UserRepository $userRepository)
  {
    $this->auth = $auth;
    $this->userRepository = $userRepository;
  }

  public function getLogin()
  {
    return view('auth::login');
  }
}