<?php namespace Jiko\Auth;

class MSUser extends OAuthUser
{
  function __construct($attributes = [])
  {
    parent::__construct($attributes);
  }

  public function user()
  {
    return $this->hasOne('Jiko\Gaming\Models\Multistreamer\User', 'id', 'oauth_id');
  }
}