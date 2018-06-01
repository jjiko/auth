<?php namespace Jiko\Auth;

use Illuminate\Database\Eloquent\Model;

class OAuthUser extends Model
{
  protected $table = 'oauth_users';
  protected $guarded = ['id'];
  protected $connection = "auth";
  protected $refreshToken;

  public function user()
  {
    return $this->belongsTo('Jiko\Auth\User');
  }

  public function meta()
  {
    return $this->hasMany('Jiko\Auth\OAuthMeta');
  }

  public function scopeProvider($query, $name)
  {
    return $query->where('provider', $name);
  }
}