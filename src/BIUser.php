<?php

namespace Jiko\Auth;

class BIUser
{
  function __construct(OAuthUser $user)
  {
    $this->session = $user->token;
    $this->system = $user->oauth_id;
    $this->connection = $user->RAW;
  }

  public function user()
  {
    return $this->belongsTo('Jiko\Auth\User');
  }
}