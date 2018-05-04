<?php

namespace Jiko\Auth\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AuthEventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    'Illuminate\Auth\Events\Login' => ['Jiko\Auth\Http\Controllers\AuthController@touch'],

    SocialiteWasCalled::class => [
      'SocialiteProviders\Discord\DiscordExtendSocialite@handle',
      'SocialiteProviders\Nest\NestExtendSocialite@handle',
      'SocialiteProviders\Twitch\TwitchExtendSocialite@handle',
      'SocialiteProviders\Spotify\SpotifyExtendSocialite@handle',
      'SocialiteProviders\Steam\SteamExtendSocialite@handle'
    ],
  ];
}