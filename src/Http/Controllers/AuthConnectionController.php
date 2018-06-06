<?php namespace Jiko\Auth\Http\Controllers;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Jiko\Http\Controllers\Controller;
use Jiko\Auth\OAuthUser;

use Illuminate\Support\Facades\Crypt;
use Larabros\Elogram\Client as ElogramClient;

class AuthConnectionController extends Controller
{

  public function redirect($provider)
  {
    $providerKey = \Config::get('services.' . $provider);
    if (empty($providerKey)) {
      return view('page.status')->with('error', 'Invalid request.');
    }

    switch ($provider) {
      case "blueiris":
        return redirect()->route('home::setup');

      case "eight":
        return redirect()->route('eight::setup');

      case "nest":
        return \Socialite::driver($provider)->redirect();

      case "twitch":
        return \Socialite::driver($provider)->scopes([
          'channel_read',
          'channel_editor',
          'channel_feed_edit',
          'user_read',
          'user_subscriptions'
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

  public function callback($provider)
  {
    // assign this provider to a user instead of creating a user from them
    $authUser = $request->user();

    switch ($provider) {
      case "blueiris":
        $connectUser = OAuthUser::firstOrNew([
          'provider' => $provider,
          'oauth_id' => request()->input('system'),
          'user_id' => $authUser->id
        ]);
        $connectUser->token = request()->input('session');
        $connectUser->RAW = json_encode(request()->input());
        break;

      case "eight":
        $headers = [
          'Content-Type' => 'application/x-www-form-urlencoded',
          'connection' => 'keep-alive',
          'user-agent' => 'okhttp/3.6.0',
          'accept' => '*/*',
          'authority' => 'app-api.8slp.net'
        ];
        $client = new HttpClient();
        try {
          $resp = $client->request('POST', 'https://app-api.8slp.net/v1/login', [
            'headers' => $headers,
            'form_params' => [
              'email' => request()->input('eight_email'),
              'password' => request()->input('eight_password')
            ]
          ]);
          $data = json_decode($resp->getBody()->getContents());
        } catch (GuzzleException $e) {
          dd($e->getMessage());
        }

        if (!property_exists($data, "session")) {
          return ['status' => 400, 'message' => 'No session data in response', $data];
        }

        $connectUser = OAuthUser::firstOrNew([
          'provider' => 'eight',
          'user_id' => $authUser->id
        ]);
        $connectUser->token = $data->session->token;
        $connectUser->oauth_id = $data->session->userId;
        $connectUser->expiresIn = $data->session->expirationDate;
        $connectUser->RAW = json_encode([
          'email' => request()->input('email'),
          'password' => Crypt::encryptString(request()->input('password')),
          'session' => $data->session
        ]);
        break;

      case "nest":
        $user = \Socialite::driver($provider)->user();
        $connectUser = OAuthUser::firstOrNew([
          'provider' => $provider,
          'oauth_id' => $user->metadata['user_id'],
          'user_id' => $authUser->id
        ]);

        $connectUser->token = $user->metadata['access_token'];
        $connectUser->RAW = json_encode($user);
        break;

      case "instagram":
        $client = new ElogramClient(
          config('services.instagram.client_id'),
          config('services.instagram.client_secret'),
          null, // access token
          config('services.instagram.redirect')
        );
        $resp = $client->getAccessToken(request()->input('code'));
        $values = $resp->getValues();
        $connectUser = OAuthUser::firstOrNew([
          'provider' => $provider,
          'oauth_id' => $values['user']['id'],
          'user_id' => $authUser->id
        ]);
        $connectUser->token = $resp->getToken();
        $connectUser->RAW = json_encode($values);
        break;

      default:
        $user = \Socialite::driver($provider)->user();
        $connectUser = OAuthUser::firstOrNew([
          'provider' => $provider,
          'oauth_id' => $user->id,
          'user_id' => $authUser->id
        ]);
        $connectUser->token = isset($user->token) ? $user->token : null;
        $connectUser->tokenSecret = isset($user->tokenSecret) ? $user->tokenSecret : null;
        $connectUser->refreshToken = isset($user->refreshToken) ? $user->refreshToken : null;
        $connectUser->RAW = json_encode($user);
    }

    $connectUser->save();

    return redirect('/user');
  }
}