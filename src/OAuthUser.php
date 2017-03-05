<?php namespace Jiko\Auth;

use Illuminate\Database\Eloquent\Model;

class OAuthUser extends Model {
  protected $table = 'oauth_users';

  public function user()
  {
    return $this->belongsTo('Jiko\Auth\User');
  }
}