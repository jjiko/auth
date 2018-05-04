<?php namespace Jiko\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Jiko\Auth\Commands\MetaCommand;

class AuthServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->loadViewsFrom(__DIR__ . '/../views', 'auth');

    if ($this->app->runningInConsole()) {
      $this->commands([
        MetaCommand::class
      ]);
    }
  }

  public function register()
  {
    $this->app->register('Jiko\Auth\Providers\AuthEventServiceProvider');
    $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    $this->mergeConfigFrom(
      __DIR__ . '/../config/services.php', 'services'
    );
    $this->mergeConfigFrom(
      __DIR__ . '/../config/database.php', 'database.connections'
    );
  }
}