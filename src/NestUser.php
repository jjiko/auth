<?php namespace Jiko\Auth;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;

class NestUser extends OAuthUser
{
  public $token;

  function __construct(OAuthUser $user)
  {
    $this->token = $user->token;
  }


  protected function getCamera($id)
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $this->token,
    ];
    try {
      $initialResponse = $this->getHttpClient()->request('GET', "https://developer-api.nest.com/devices/cameras", [
        'allow_redirects' => false,
        'headers' => $headers
      ]);
      if ($initialResponse->getStatusCode() == 307) {
        $response = $this->getHttpClient()->request('GET', array_first($initialResponse->getHeader('Location')), ['headers' => $headers]);
      }
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
      return $e;
    }
    return json_decode($response->getBody()->getContents());
  }

  protected function getData()
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $this->token,
    ];
    try {
      $initialResponse = $this->getHttpClient()->request('GET', 'https://developer-api.nest.com', [
        'allow_redirects' => false,
        'headers' => $headers
      ]);
      if ($initialResponse->getStatusCode() == 307) {
        $response = $this->getHttpClient()->request('GET', array_first($initialResponse->getHeader('Location')), ['headers' => $headers]);
      }
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
      return $e;
    }
    return json_decode($response->getBody()->getContents());
  }

  public function getDataAttribute()
  {
    return $this->getData();
  }

  public function camera($id)
  {
    return $this->getCamera($id);
  }

  public function getHttpClient()
  {
    return new HttpClient();
  }
}