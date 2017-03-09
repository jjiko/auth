<?php namespace Jiko\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->loadViewsFrom(__DIR__ . '/../views', 'auth');
  }

  public function register()
  {
    $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    $this->mergeConfigFrom(
      __DIR__ . '/../config/services.php', 'services'
    );
  }
}