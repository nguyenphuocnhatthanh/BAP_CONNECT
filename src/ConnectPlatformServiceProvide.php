<?php
namespace Bap\ConnectPlatform;

use Illuminate\Support\ServiceProvider;

class ConnectPlatformServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/platform.php' => config_path('platform.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/platform.php', 'platform');
        $this->app->bind('platform', function($app) {
            return new ConnectPlatform(
                $app['config']['platform.url'],
                new AccessToken()
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['platform'];
    }
}