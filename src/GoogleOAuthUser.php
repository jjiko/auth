<?php namespace Jiko\Auth;

use Google_Client;

class GoogleOAuthUser extends OAuthUser
{
  protected $client;

  function __construct(array $attributes = [])
  {
    try {
      $this->client = new Google_Client();
      $this->client->setAuthConfig(base_path('google_client_secrets.json'));
    } catch (\Google_Exception $e) {
      logger($e->getMessage());
    }

    parent::__construct($attributes);
  }

  public function getTokenAttribute()
  {
    if ((time() - strtotime($this->updated_at)) > $this->expiresIn) {
      // get a new token
      $this->client->refreshToken($this->refreshToken);
      $newtoken = $this->client->getAccessToken();
      $this->token = $newtoken['access_token'];
      $this->refreshToken = $newtoken['refresh_token'];
      $this->save();
    }

    return $this->token;
  }
}