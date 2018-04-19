<?php namespace Jiko\Auth;

use Twitter;

class TwitterUser
{
  protected $clientId;
  protected $token;
  protected $twitter;
  protected $secret;

  function __construct(OAuthUser $user)
  {
    // private
    $this->clientId = env('TWITTER_API_KEY');

    $this->id = $user->oauth_id;
    $this->token = $user->token;
    $this->secret = $user->tokenSecret;

  }

  public function followers()
  {
    return;
  }

  public function friendships()
  {
    $notFollowingBack = [];
    $followingBack = [];
    if ($twitter = $this->getHttpClient()) {
      try {
        $resp = $twitter->request('friends/ids', 'GET');
        $ids = array_chunk($resp->ids, 100);
        foreach ($ids as $i => $chunk) {
          $resp = $twitter->request('friendships/lookup', 'GET', ['user_id' => join(",", $chunk)]);
          foreach ($resp as $j => $friend) {
            if (in_array("followed_by", $friend->connections)) {
              $followingBack[] = $friend;
              continue;
            }

            $notFollowingBack[] = $friend;
          }
          break;
        }

        return (object)["followingBack" => $followingBack, "notFollowingBack" => $notFollowingBack];

      } catch (\TwitterException $e) {
        return $e->getMessage();
      }
    }
    return $twitter;
  }

  public function tweet($status, $media = [])
  {
    if ($twitter = $this->getHttpClient()) {
      try {
        $resp = $twitter->send($status, $media);
      } catch (\TwitterException $e) {
        return $e->getMessage();
      }

      return $resp;
    }

    return $twitter;
  }

  public function getToken()
  {
    return $this->token;
  }

  public function getSecret()
  {
    return $this->secret;
  }

  public function getHttpClient()
  {
    try {
      if (!$this->twitter) {
        $this->twitter = new Twitter(env('TWITTER_API_KEY'), env('TWITTER_API_SECRET'), $this->token, $this->secret);
      }
    } catch (\TwitterException $e) {
      // Output error to log?
      return false;
    }

    return $this->twitter;
  }
}