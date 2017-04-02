<?php namespace Jiko\Auth;

use Illuminate\Database\Eloquent\Model;

class OAuthUser extends Model {
  protected $table = 'oauth_users';
  protected $guarded = ['id'];

  public function user()
  {
    return $this->belongsTo('Jiko\Auth\User');
  }

  public function scopeProvider($query, $name)
  {
    return $query->where('provider', $name);
  }
}