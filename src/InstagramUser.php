<?php namespace Jiko\Auth;

use Larabros\Elogram\Client as InstagramClient;
use League\OAuth2\Client\Token\AccessToken;
use Illuminate\Support\Collection;

class InstagramUser
{
  protected $user; // OAuthUser
  protected $client;

  function __construct(OAuthUser $user)
  {
    $this->user = $user;
    $this->client = new InstagramClient(
      getenv('INSTAGRAM_CLIENT_ID'),
      getenv('INSTAGRAM_CLIENT_SECRET'),
      getenv('INSTAGRAM_REDIRECT_URI')
    );
    $this->client->setAccessToken(new AccessToken(['access_token' => $this->user->token]));
  }

  public function recent($params = [])
  {
    $limit = isset($params['count']) ? $params['count'] : 8;
    $id = isset($params['id']) ? $params['id'] : 'self';
    return new Collection($this->client->users()->getMedia($id, $limit)->get());
  }
}