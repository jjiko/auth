<?php namespace Jiko\Auth;

use Google_Client;
use Google_Exception;

class GoogleUser
{
  protected $user; // OAuthUser
  protected $client;
  protected $refreshToken;
  public $updated_at;
  public $expiresIn;

  function __construct(OAuthUser $user)
  {
    $this->user = $user;
    $this->refreshToken = $user->refreshToken;
    try {
      $this->client = new Google_Client();
      $this->client->setAuthConfig(base_path('google_client_secrets.json'));
    } catch (Google_Exception $e) {
      logger($e->getMessage());
    }
  }

  public function getClient()
  {
    return $this->client;
  }

  public function getTokenAttribute()
  {
    if ((time() - strtotime($this->updated_at)) > $this->expiresIn) {
      // get a new token
      $this->client->refreshToken($this->user->refreshToken);
      $newToken = $this->client->getAccessToken();
      $this->user->token = $newToken['access_token'];
      $this->user->refreshToken = $newToken['refresh_token'];
      $this->user->save();
    }

    return $this->token;
  }
}