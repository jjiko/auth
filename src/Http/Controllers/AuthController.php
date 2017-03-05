<?php namespace Jiko\Auth\Http\Controllers;

use Jiko\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Jiko\Auth\Role;
use Jiko\Auth\OAuthUser;
use Jiko\Auth\User;
use Jiko\Auth\UserRepository;

class AuthController extends Controller
{
  protected $auth;

  protected $userRepository;

  public function __construct(Guard $auth, UserRepository $userRepository)
  {
    parent::__construct();
    $this->auth = $auth;
    $this->userRepository = $userRepository;
  }

  public function getLogin()
  {
    $this->content('auth::login');
  }

  public function redirectToProvider($provider)
  {
    $providerKey = \Config::get('services.' . $provider);
    if (empty($providerKey)) {
      return view('page.status')->with('error', 'Invalid request.');
    }

    return \Socialite::driver($provider)->redirect();
  }

  public function getRegister()
  {
    return 'Register';
  }

  public function getUser()
  {
    $this->content('auth::user');
  }

  public function handleProviderCallback($provider)
  {
    $user = \Socialite::driver($provider)->user();
    $socialUser = null;

    // check if email is present
    $userCheck = User::where('email', '=', $user->email)->first();
    if(!empty($userCheck)) {
      $socialUser = $userCheck;
    }
    else {
      $sameSocialId = OAuthUser::where('oauth_id', '=', $user->id)->where('provider', '=', $provider)->first();

      if(empty($sameSocialId))
      {
        $newSocialUser = new User;
        $newSocialUser->email = $user->email;
        $name = explode(' ', $user->name);
        $newSocialUser->first_name = $name[0];
        $newSocialUser->last_name = $name[1];
        $newSocialUser->save();

        $socialData = new OAuthUser;
        $socialData->oauth_id = $user->id;
        $socialData->provider = $provider;
        $newSocialUser->OAuthUser()->save($socialData);

        // add role
        $role  = Role::whereName('user')->first();
        $newSocialUser->assignRole($role);

        $socialUser = $newSocialUser;
      }
      else {
        // load this existing user
        $socialUser = $sameSocialId->user;
      }
    }

    $this->auth->login($socialUser, true);

    return redirect('/');
  }
}