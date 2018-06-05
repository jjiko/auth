<?php namespace Jiko\Auth\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Jiko\Auth\Commands\MetaCommand;
use Zizaco\Entrust\EntrustFacade;
use Zizaco\Entrust\Middleware\EntrustAbility;
use Zizaco\Entrust\Middleware\EntrustPermission;
use Zizaco\Entrust\Middleware\EntrustRole;

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

    $this->app['router']->aliasMiddleware('role', EntrustRole::class);
    $this->app['router']->aliasMiddleware('permission', EntrustPermission::class);
    $this->app['router']->aliasMiddleware('ability', EntrustAbility::class);

    $this->app['auth']->provider('cacheableEloquent',
      function ($app, $config) {
        $config['model']::updated(function ($model) {
          CacheableEloquentUserProvider::clearCache($model);
        });
        return new CacheableEloquentUserProvider($app->make('Illuminate\Contracts\Hashing\Hasher'), $config['model']);
      }
    );
  }

  public function register()
  {

    $this->app->booting(function () {
      $loader = AliasLoader::getInstance();
      $loader->alias(EntrustFacade::class, 'Entrust');
    });

    // Update config
    $this->mergeConfigFrom(
      __DIR__ . '/../config/auth.providers.cacheable-users.php', 'auth.providers.cacheable-users'
    );

    $this->mergeConfigFrom(
      __DIR__ . '/../config/database.connections.auth.php', 'database.connections.auth'
    );
    $this->mergeConfigFrom(
      __DIR__ . '/../config/services.php', 'services'
    );
    $this->mergeConfigFrom(
      __DIR__ . '/../config/entrust.php', 'entrust'
    );

    // Routes
    $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
  }
}