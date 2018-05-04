<?php
namespace Jiko\Auth;

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

  public function getClient()
  {
     return $this->client;
  }

  public function next($params = [])
  {
    $media = $this->client
      ->users()
      ->getMedia(
        array_get($params, 'id', 'self'),
        array_get($params, 'count', 20),
        array_get($params, 'min_id', null),
        array_get($params, 'max_id', null)
      )->getRaw();
    $collection = new Collection(array_get($media, 'data', []));

    return ['data' => $collection, 'pagination' => array_get($media, 'pagination', [])];
  }

  public function recent($params = [])
  {
    $media = $this->client
      ->users()
      ->getMedia(array_get($params, 'id', 'self'), array_get($params, 'count', 20))
      ->getRaw();
    $collection = new Collection(array_get($media, 'data', []));

    return ['data' => $collection, 'pagination' => array_get($media, 'pagination', [])];
  }

  public function relationship($type)
  {

  }

  public function follows()
  {
    $resp = $this->client->users()->follows();
    return $resp->get();
  }

  public function followedBy()
  {
    $resp = $this->client->users()->followedBy();
    return $resp->get();
  }
}