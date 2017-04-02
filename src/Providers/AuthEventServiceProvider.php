<?php

namespace Jiko\Auth\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AuthEventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    'Illuminate\Auth\Events\Login' => ['Jiko\Auth\Http\Controllers\AuthController@touch'],
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
      // add your listeners (aka providers) here
      'SocialiteProviders\Steam\SteamExtendSocialite@handle',
      'SocialiteProviders\Twitch\TwitchExtendSocialite@handle',
    ],
  ];
}
