<?php namespace Jiko\Auth;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;

class SpotifyUser
{
  protected $clientId;
  protected $token;
  protected $refreshToken;
  protected $updated_at;

  protected $model;

  function __construct(OAuthUser $user)
  {
    $this->model = $user;

    // private
    $this->clientId = env('SPOTIFY_KEY');
    $this->clientSecret = env('SPOTIFY_SECRET');

    $this->id = $user->oauth_id;
    $this->refreshToken = $user->refreshToken;
    $this->expiresIn = $user->expiresIn;
    $this->endpoint = 'https://api.spotify.com';
    $this->updated_at = $user->updated_at;

    if (Carbon::now()->diffInSeconds($user->updated_at) > $this->expiresIn) { // ->tz('America/New_York')
      // get a new token
      $token = $this->refreshToken();

      // update
      $user->token = $token['access_token'];
      $user->save();
      $this->updated_at = Carbon::now();

      $this->token = $token['access_token'];
    } else {
      $this->token = $user->token;
    }
  }

  public function getToken()
  {
    return $this->token;
  }

  protected function refreshToken()
  {
    try {
      $options = [
        'headers' => [
          'Authorization' => 'Basic ' . base64_encode(sprintf("%s:%s", $this->clientId, $this->clientSecret))
        ],
        'form_params' => [
          'grant_type' => "refresh_token",
          'refresh_token' => $this->refreshToken
        ]
      ];
      $response = $this->getHttpClient()->post(
        "https://accounts.spotify.com/api/token",
        $options
      );

      return json_decode($response->getBody()->getContents(), true);

    } catch (ClientException $e) {
      dd($e->getMessage());
    }
  }

  public function getHttpClient()
  {
    return new HttpClient();
  }
}