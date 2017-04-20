<?php namespace Jiko\Auth;

use GuzzleHttp\Exception\ClientException;
use Jiko\Gaming\Twitch\TwitchChannel;

class TwitchUser
{
  protected $clientId;
  protected $token;
  protected $refreshToken;

  function __construct(OAuthUser $user)
  {
    // private
    $this->clientId = env('TWITCH_CLIENT_ID');

    $this->id = $user->oauth_id;
    $this->token = $user->token;
    $this->refreshToken = $user->refreshToken;
  }

  public function channel()
  {
    try {
      $response = $this->getHttpClient()->get(
        'https://api.twitch.tv/kraken/channel', [
        'headers' => [
          'Accept' => 'application/vnd.twitchtv.v3+json',
          'Authorization' => 'OAuth ' . $this->token,
          'Client-ID' => $this->clientId,
        ],
      ]);
    } catch (ClientException $e) {
      return $e;
    }
    return (new TwitchChannel(json_decode($response->getBody()->getContents())));
  }

  public function channelById($id)
  {
    $response = $this->getHttpClient()->get(
      'https://api.twitch.tv/kraken/channels/' . $id, [
      'headers' => [
        'Client-ID' => $this->clientId,
        'Accept' => 'application/vnd.twitchtv.v3+json'
      ],
    ]);
    return json_decode($response->getBody()->getContents());
  }

  public function channelUpdate($id, $props)
  {
    try {
      $response = $this->getHttpClient()->put(
        'https://api.twitch.tv/kraken/channels/' . $id, [
        'headers' => [
          'Accept' => 'application/vnd.twitchtv.v3+json',
          'Authorization' => 'OAuth ' . $this->token,
          'Client-ID' => $this->clientId,
        ],
        'body' => [
          'channel' => [
            'status' => $props->status,
            'game' => $props->game
          ]
        ]
      ]);
      return json_decode($response->getBody()->getContents(), true);
    } catch (ClientException $e) {
      return ['error' => $e->getMessage()];
    }
  }

  public function getHttpClient()
  {
    return new \GuzzleHttp\Client();
  }
}