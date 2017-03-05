<?php namespace Jiko\Auth\Traits;

use Input;
use ReCaptcha\ReCaptcha;

class CaptchaTrait
{
  public function captchaCheck()
  {
    $response = Input::get('g-recaptcha-response');
    $remoteip = Input::server('REMOTE_ADDR');
    $secret = env('RE_CAP_SECRET');

    $recaptcha = new ReCaptcha($secret);
    $resp = $recaptcha->verify($response, $remoteip);
    if ($resp->isSuccess()) {
      return true;
    }
    return false;
  }
}