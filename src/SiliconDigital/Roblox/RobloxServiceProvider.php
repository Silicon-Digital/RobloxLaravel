<?php

namespace SiliconDigital\Roblox;

use Illuminate\Support\ServiceProvider;

class RobloxServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app ?: app();

        $appVersion = method_exists($app, 'version') ? $app->version() : $app::VERSION;

        $laravelVersion = substr($appVersion, 0, strpos($appVersion, '.'));

        $isLumen = false;

        if (strpos(strtolower($laravelVersion), 'lumen') !== false) {
            $isLumen = true;
        }

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'roblox');

        if ($isLumen) {
            $this->publishes([
                __DIR__.'/../config/config.php' => base_path('config/roblox.php'),
            ]);
        } else {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('roblox.php'),
            ]);
        }

        $this->app->singleton(Roblox::class, function () use ($app) {
            return new Roblox($app['config'], $app['session.store']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['roblox'];
    }
}
