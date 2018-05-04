<?php

namespace Jiko\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Jiko\Activity\Traits\GamingUserTrait;
use Jiko\Home\Models\Home;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
  use Authenticatable, CanResetPassword, EntrustUserTrait, GamingUserTrait;

  protected $connection = 'auth';

  protected $table = 'users';

  protected $fillable = ['name', 'email', 'password'];

  protected $hidden = ['password', 'remember_token'];

  protected $with = ['roles'];

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

  public function getInstagramAttribute($value)
  {
    if ($instagramUser = $this->OAuthUser()->where('provider', 'instagram')->first()) {
      return (new InstagramUser($instagramUser));
    }

    return $instagramUser; // null
  }

  public function getFacebookAttribute($value)
  {
    if ($facebookUser = $this->OAuthUser()->where('provider', 'facebook')->first()) {
      return (new FacebookUser($facebookUser));
    }
    return $facebookUser;
  }

  public function getMultistreamerAttribute()
  {
    if ($msUser = $this->OAuthUser()->where('provider', 'multistreamer')->first()) {
      return (new MSUser($msUser->toArray()));
    }

    return $msUser;
  }

  public function TSUser()
  {
    return $this->hasOne('Jiko\Auth\TSUser', 'user_id');
  }

  public function getTwitterAttribute($value)
  {
    if ($twitterUser = $this->OAuthUser()->where('provider', 'twitter')->first()) {
      return (new TwitterUser($twitterUser));
    }

    return $twitterUser;
  }

  public function getNestAttribute($value)
  {
    if ($nestUser = $this->OAuthUser()->where('provider', 'nest')->first()) {
      return (new NestUser($nestUser));
    }
  }

  public function getTwitchAttribute($value)
  {
    if ($twitchUser = $this->OAuthUser()->where('provider', 'twitch')->first()) {
      return (new TwitchUser($twitchUser));
    }
    return $twitchUser;
  }

  public function getEightAttribute()
  {
    if ($eightUser = $this->OAuthUser()->where('provider', 'eight')->first()) {
      return (new EightUser($eightUser));
    }

    return $eightUser;
  }

  public function getBlueirisAttribute($value)
  {
    if ($blueirisUser = $this->OAuthUser()->where('provider', 'blueiris')->first()) {
      return (new BIUser($blueirisUser));
    }
    return $blueirisUser;
  }

  public function getHomeAttribute()
  {
    if ($home = Home::where('owner_id', $this->id)->first()) {
      return $this->OAuthUser()->where('provider', $home->type)->first();
    }

    return $home;
  }

  public function getSpotifyAttribute($value)
  {
    if ($spotifyUser = $this->OAuthUser()->where('provider', 'spotify')->first()) {
      return (new SpotifyUser($spotifyUser));
    }
    return $spotifyUser;
  }

  public function getPlaystationAttribute($value)
  {
    if ($playstationUser = $this->OAuthUser()->where('provider', 'playstation')->first()) {
      return (new PSNUser($playstationUser));
    }

    return $playstationUser;
  }

  public function getNameAttribute($value)
  {
    return (empty($value)) ? vsprintf('%s %s', [$this->first_name, $this->last_name]) : $value;
  }

  public function games()
  {
    return $this->belongsToMany('Jiko\Gaming\Models\Game', 'user_game', 'user_id', 'game_id')->withPivot('platform_id', 'status', 'live');
  }

  public function platforms()
  {
    return $this->belongsToMany('Jiko\Gaming\Models\Platform', 'user_game', 'user_id', 'platform_id');
  }
}