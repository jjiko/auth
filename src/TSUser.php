<?php namespace Jiko\Auth;

use Illuminate\Database\Eloquent\Model;

class TSUser extends Model {
  protected $table = 'ts_users';
  protected $guarded = ['id'];

  public function user()
  {
    return $this->belongsTo('Jiko\Auth\User');
  }
}