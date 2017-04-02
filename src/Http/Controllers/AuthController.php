<?php namespace Jiko\Auth\Http\Controllers;

use Jiko\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Jiko\Auth\OAuthUser;
use Jiko\Auth\Permission;
use Jiko\Auth\Role;
use Jiko\Auth\User;
use Jiko\Auth\UserRepository;

use Illuminate\Support\Facades\Input;

use Google_Client, Google_Service_Plus;
use Larabros\Elogram\Client as ElogramClient;

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
    \Auth::logout();
    return redirect('/');
  }

  public function getOnce()
  {
    //    $user = \Jiko\Auth\User::where('email', '=', 'joejiko@gmail.com')->first();
    //    $user->attachRole(1);
    $perm = new Permission();
    $perm->name = 'create-page';
    $perm->display_name = 'Create Page';
    $perm->description = 'Create new pages on site.';
    $perm->save();

    $editUser = new Permission();
    $editUser->name = 'modify-user';
    $editUser->display_name = 'Modify Users';
    $editUser->description = 'modify existing users';
    $editUser->save();

    $admin = Role::find(1);
    $admin->attachPermission([$perm, $editUser]);
  }

  public function redirectToProvider($provider)
  {
    $providerKey = \Config::get('services.' . $provider);
    if (empty($providerKey)) {
      return view('page.status')->with('error', 'Invalid request.');
    }

    switch ($provider) {
      case "google":
        $client = new Google_Client();
        $client->setAuthConfig(base_path('/google_client_secrets.json'));
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->setScopes([
          'profile',
          'email',
          'https://www.googleapis.com/auth/calendar',
          'https://www.googleapis.com/auth/youtube'
        ]);
        $client->setApprovalPrompt('force');
        $client->setRedirectUri('http://' . Input::server('HTTP_HOST') . '/auth/handler/google');
        return redirect(filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
        return \Socialite::driver($provider)->scopes([
          'profile',
          'email',
          'https://www.googleapis.com/auth/calendar',
          'https://www.googleapis.com/auth/youtube'
        ])->with(['access_type' => 'offline'])->redirect();

      case "facebook":
        return \Socialite::driver($provider)->scopes([
          'email',
          'user_likes',
          'publish_actions',
          'manage_pages',
          'publish_pages'
        ])->redirect();

      case "instagram":
        $client = new ElogramClient(
          config('services.instagram.client_id'),
          config('services.instagram.client_secret'),
          null, // access token
          config('services.instagram.redirect')
        );

        return redirect($client->getLoginUrl(config('services.instagram.scopes')));
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

  public function handleConnectionCallback($provider)
  {
    // assign this provider to a user instead of creating a user from them
    $authUser = $this->auth->user();
    $connectUser = new OAuthUser;

    switch ($provider) {
      case "instagram":
        $client = new ElogramClient(
          config('services.instagram.client_id'),
          config('services.instagram.client_secret'),
          null, // access token
          config('services.instagram.redirect')
        );
        $resp = $client->getAccessToken(request()->input('code'));
        $values = $resp->getValues();
        $connectUser->token = $resp->getToken();
        $connectUser->user_id = $authUser->id;
        $connectUser->oauth_id = $values['user']['id'];
        $connectUser->provider = $provider;
        $connectUser->RAW = json_encode($values);
        break;

      default:
        $user = \Socialite::driver($provider)->user();
        $connectUser->user_id = $authUser->id;
        $connectUser->oauth_id = $user->id;
        $connectUser->token = isset($user->token) ? $user->token : null;
        $connectUser->tokenSecret = isset($user->tokenSecret) ? $user->tokenSecret : null;
        $connectUser->provider = $provider;
        $connectUser->RAW = json_encode($user);
    }

    $connectUser->save();

    return redirect('/user');
  }

  public function handleProviderCallback($provider)
  {
    switch ($provider) {
      case "google":

        $client = new Google_Client();
        $client->setAuthConfig(base_path('google_client_secrets.json'));
        $client->setRedirectUri('http://' . Input::server('HTTP_HOST') . '/auth/handler/google');
        $client->authenticate(Input::get('code'));
        $plus = new Google_Service_Plus($client);
        $guser = (array)$plus->people->get('me')->toSimpleObject();
        $user = (new \SocialiteProviders\Manager\OAuth2\User)->setRaw($guser)->map([
          'id' => $guser['id'], 'nickname' => array_get($guser, 'nickname'), 'name' => $guser['displayName'],
          'email' => $guser['emails'][0]->value, 'avatar' => array_get($guser, 'image')->url,
        ]);
        $token = $client->getAccessToken();
        $user->token = $token['access_token'];
        $user->refreshToken = $client->getRefreshToken();
        $user->expiresIn = $token['expires_in'];
        break;

      default:
        $user = \Socialite::driver($provider)->user();
        $socialUser = null;
        $userCheck = null;
    }

    // check if email is present
    if (!is_null($user->email)) {
      $userCheck = User::where('email', '=', $user->email)->first();
    }
    if (!empty($userCheck)) {
      $socialUser = $userCheck;

      // User exists but not with this provider
      if (!$oauthuser = OAuthUser::where('user_id', $socialUser->id)->where('provider', $provider)->first()) {
        $oauthuser = new OAuthUser;
        $oauthuser->user_id = $socialUser->id;
        $oauthuser->oauth_id = $user->id;
        $oauthuser->provider = $provider;
      }
      // update token?
      $oauthuser->token = isset($user->token) ? $user->token : null;
      $oauthuser->tokenSecret = isset($user->tokenSecret) ? $user->tokenSecret : null;
      $oauthuser->refreshToken = isset($user->refreshToken) ? $user->refreshToken : null;
      $oauthuser->expiresIn = isset($user->expiresIn) ? $user->expiresIn : null;
      $oauthuser->RAW = json_encode($user);
      $oauthuser->save();
    } else {
      $sameSocialId = OAuthUser::where('oauth_id', '=', $user->id)->where('provider', '=', $provider)->first();

      if (empty($sameSocialId)) {
        $newSocialUser = new User;
        $newSocialUser->email = $user->email;
        $newSocialUser->name = $user->name;
        if (strpos($user->name, ' ')) {
          $name = explode(' ', $user->name);
          $newSocialUser->first_name = $name[0];
          $newSocialUser->last_name = $name[1];
        } else {
          $newSocialUser->name = $user->name;
          $newSocialUser->first_name = $user->name;
        }
        $newSocialUser->save();

        $socialData = new OAuthUser;
        $socialData->oauth_id = $user->id;
        $socialData->provider = $provider;
        $socialData->token = $user->token;
        $socialData->refreshToken = isset($user->refreshToken) ? $user->refreshToken : null;
        $socialData->tokenSecret = isset($user->tokenSecret) ? $user->tokenSecret : null;
        $socialData->expiresIn = $user->expiresIn;
        $newSocialUser->OAuthUser()->save($socialData);

        // add role
        $role = Role::whereName('user')->first();
        $newSocialUser->attachRole($role);

        $socialUser = $newSocialUser;
      } else {
        // load this existing user
        $socialUser = $sameSocialId->user;
      }
    }

    $this->auth->login($socialUser, true);

    return redirect('/');
  }
}