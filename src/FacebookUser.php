<?php namespace Jiko\Auth;

use Facebook\Authentication\AccessToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FacebookUser
{
  protected $user; // OAuthUser
  protected $client;

  function __construct(OAuthUser $user)
  {
    $this->user = $user;
    $this->client = new \Facebook\Facebook([
      'app_id' => env('FACEBOOK_APP_ID'),
      'app_secret' => env('FACEBOOK_APP_SECRET'),
      'default_graph_version' => 'v2.8',
      //'default_access_token' => '{access-token}', // optional
    ]);
  }

  public function recent($params = [])
  {
    try {
      // Get the \Facebook\GraphNodes\GraphUser object for the current user.
      // If you provided a 'default_access_token', the '{access-token}' is optional.
      $token = new AccessToken($this->user->token);
      $response = $this->client->get('/me/posts?fields=message,created_time,type,status_type,attachments,likes,comments,link,picture,source', $token);
    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      Log::error('Graph returned an error: ' . $e->getMessage());
      exit;
    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      Log::error('Facebook SDK returned an error: ' . $e->getMessage());
      exit;
    }
    return $response->getGraphEdge();
  }
}