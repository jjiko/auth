<?php namespace Jiko\Auth;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class EightUser extends OAuthUser
{
  public $token;
  protected $devices;

  function __construct(OAuthUser $user)
  {
    $this->token = $user->token;
    $this->devices = [];
  }

  protected function getData()
  {
    $headers = [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'connection' => 'keep-alive',
      'user-agent' => 'okhttp/3.6.0',
      'accept' => '*/*',
      'authority' => 'app-api.8slp.net',
      'session-token' => $this->token
    ];
    try {
      $usr_resp = $this->getHttpClient()->request('GET', 'https://app-api.8slp.net/v1/users/me', [
        'headers' => $headers
      ]);
      $respData = json_decode($usr_resp->getBody()->getContents());
      foreach ($respData->user->devices as $deviceId) {
        $dev_resp = $this->getHttpClient()->request('GET', 'https://app-api.8slp.net/v1/devices/' . $deviceId);
        $devData = json_decode($dev_resp->getBody()->getContents());
        $this->devices[] = $devData->result;
      }
    } catch (GuzzleException $e) {
      return $e;
    }
    return $this->devices;
  }

  public function stopHeating($device)
  {
    return $this->startHeating($device, 10, 0);
  }

  /**
   * @param $device device object
   * @param int $level temperature range 10-100
   * @param int $duration time to heat in minutes (0 to stop heating)
   * @return json updated device parameters
   */
  public function startHeating($device, $level = 10, $duration = 0)
  {
    // convert to minutes
    $duration *= 60;
    $headers = [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'connection' => 'keep-alive',
      'user-agent' => 'okhttp/3.6.0',
      'accept' => '*/*',
      'authority' => 'app-api.8slp.net',
      'session-token' => $this->token
    ];
    try {
      // @todo get side using $device->leftUserId === $this->oauth_id or make option to sync sides
      $res = $this->getHttpClient()->request('PUT', 'https://app-api.8slp.net/v1/devices/' . $device->deviceId, [
        'headers' => $headers,
        'form_params' => [
          'leftHeatingDuration' => $duration,
          'leftTargetHeatingLevel' => $level,
          'rightHeatingDuration' => $duration,
          'rightTargetHeatingLevel' => $level
        ]
      ]);
    } catch (GuzzleException $e) {
      return json_encode(['status' => 400, 'message' => $e->getMessage()]);
    }

    return json_decode($res->getBody()->getContents());
  }

  public function getDataAttribute()
  {
    return $this->getData();
  }

  public function getHttpClient()
  {
    return new HttpClient();
  }
}