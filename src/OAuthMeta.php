<?php namespace Jiko\Auth;

use Illuminate\Database\Eloquent\Model;

class OAuthMeta extends Model
{
  protected $table = 'oauth_meta';
  protected $guarded = ['id'];

  protected $connection = "auth";

  public function OAuthUser()
  {
    return $this->hasOne('Jiko\Auth\OAuthUser');
  }

  public function scopeKey($query, $name)
  {
    return $query->where('key', $name);
  }
}