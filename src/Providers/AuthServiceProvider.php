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

    $this->app['auth']->provider('cacheableEloquent',
      function ($app, $config) {
        $config['model']::updated(function ($model) {
          CacheableEloquentUserProvider::clearCache($model);
        });
        return new CacheableEloquentUserProvider($app['hash'], $config['model']);
      }
    );
  }

  public function register()
  {
    // Update config
    $this->mergeConfigFrom(
      __DIR__ . '/../config/services.php', 'services'
    );
    $this->mergeConfigFrom(
      __DIR__ . '/../config/auth.providers.users.php', 'auth.providers.users'
    );
    $this->mergeConfigFrom(
      __DIR__ . '/../config/database.connections.php', 'database.connections'
    );

    // Routes
    $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
  }
}