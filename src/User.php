<?php namespace Jiko\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
  use Authenticatable, CanResetPassword, EntrustUserTrait;

  protected $table = 'users';

  protected $fillable = ['name', 'email', 'password'];

  protected $hidden = ['password', 'remember_token'];

  public static $rules = [
    'first_name' => 'required',
    'last_name' => 'required',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6|max:20',
    'password_confirmation' => 'required|same:password',
    'g-recaptcha-response' => 'required'
  ];

  public static $messages = [
    'first_name.required' => 'First name is required',
    'last_name.required' => 'Last name is required',
    'email.required' => 'Email is required',
    'email.email' => 'Email is invalid',
    'password.required' => 'Password is required',
    'password.min' => 'Password needs to have at least 6 characters',
    'password.max' => 'Password cannot be longer than 20 characters',
    'g-recaptcha-response.required' => 'Captcha is required'
  ];

  public function OAuthUser()
  {
    return $this->hasMany('Jiko\Auth\OAuthUser');
  }

  public function TSUser()
  {
    return $this->hasOne('Jiko\Auth\TSUser', 'user_id');
  }

  public function getNameAttribute($value)
  {
    return (empty($value)) ? vsprintf('%s %s', [$this->first_name, $this->last_name]) : $value;
  }

  public function games()
  {
    return $this->belongsToMany('Jiko\Gaming\Models\Game', 'user_game', 'user_id', 'game_id')->withPivot('platform_id', 'status');
  }

  public function platforms()
  {
    return $this->belongsToMany('Jiko\Gaming\Models\Platform', 'user_game', 'user_id', 'platform_id');
  }
}