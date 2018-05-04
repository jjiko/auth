<?php
namespace Jiko\Auth;

use GuzzleHttp\Client as HttpClient;
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
    $this->endpoint = 'https://api.twitch.tv/kraken';
  }

  public function status()
  {
    $response = $this->getHttpClient()->get(
      "{$this->endpoint}/streams/{$this->id}", [
        'headers' => [
          'Accept' => 'application/vnd.twitchtv.v5+json',
          'Client-ID' => $this->clientId
        ]
      ]
    );

    return json_decode($response->getBody()->getContents());
  }

  public function channel()
  {
    try {
      $response = $this->getHttpClient()->get(
        "{$this->endpoint}/channel", [
        'headers' => [
          'Accept' => 'application/vnd.twitchtv.v5+json',
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
      "{$this->endpoint}/channels/$id", [
      'headers' => [
        'Client-ID' => $this->clientId,
        'Accept' => 'application/vnd.twitchtv.v5+json'
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
          'Accept' => 'application/vnd.twitchtv.v5+json',
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
    return new HttpClient();
  }
}