<?php namespace Jiko\Auth\Http\Controllers;

use Jiko\Http\Controllers\Controller;
use Jiko\Auth\OAuthUser;
use Jiko\Auth\Role;
use Jiko\Auth\User;

use Illuminate\Support\Facades\Input;

use Google_Client, Google_Service_Plus;

class AuthProviderController extends Controller
{
  public function redirect($provider)
  {
    $providerKey = \Config::get('services.' . $provider);
    if (empty($providerKey)) {
      return view('page.status')->with('error', 'Invalid request.');
    }

    switch ($provider) {
      case "google":
        try {
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
        } catch (\Google_Exception $e) {
          dd($e->getMessage());
        }
        break;

      case "facebook":
        return \Socialite::driver($provider)->scopes([
          'email',
          'user_likes',
          'publish_actions',
          'manage_pages',
          'publish_pages'
        ])->redirect();
    }

    return \Socialite::driver($provider)->redirect();
  }

  public function callback($provider)
  {
    switch ($provider) {
      case "google":

        try {
          $client = new Google_Client();
          $client->setAuthConfig(base_path('google_client_secrets.json'));
          $client->setRedirectUri('http://' . Input::server('HTTP_HOST') . '/auth/handler/google');
          $client->fetchAccessTokenWithAuthCode(Input::get('code'));
          $plus = new Google_Service_Plus($client);
          $guser = (array)$plus->people->get('me')->toSimpleObject();
          $user = (new \SocialiteProviders\Manager\OAuth2\User)->setRaw($guser)->map([
            'id' => $guser['id'],
            'nickname' => array_get($guser, 'nickname'),
            'name' => $guser['displayName'],
            'email' => $guser['emails'][0]->value,
            'avatar' => array_get($guser, 'image.url'),
          ]);
          $token = $client->getAccessToken();
          $user->token = $token['access_token'];
          $user->refreshToken = $client->getRefreshToken();
          $user->expiresIn = $token['expires_in'];
        } catch (\Google_Exception $e) {
          dd($e->getMessage());
        }
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

      // Create a new user or update existing
      $oauthuser = OAuthUser::firstOrNew([
        'user_id' => $socialUser->id,
        'oauth_id' => $user->id,
        'provider' => $provider
      ]);

      // update
      $oauthuser->token = isset($user->token) ? $user->token : null;
      $oauthuser->tokenSecret = isset($user->tokenSecret) ? $user->tokenSecret : null;
      $oauthuser->refreshToken = isset($user->refreshToken) ? $user->refreshToken : null;
      $oauthuser->expiresIn = isset($user->expiresIn) ? $user->expiresIn : null;
      $oauthuser->RAW = json_encode($user);
      $oauthuser->save();
    } else {
      // @todo idk what this is doing
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

    auth()->login($socialUser, true); // login and remember

    return redirect('/');
  }
}