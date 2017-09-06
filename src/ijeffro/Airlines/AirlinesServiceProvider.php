<?php

namespace ijeffro\Airlines;

use Illuminate\Support\ServiceProvider;

/**
 * CountryListServiceProvider
 *
 */

class AirlinesServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
    * Bootstrap the application.
    *
    * @return void
    */
    public function boot()
    {
        // The publication files to publish
        $this->publishes([__DIR__ . '/../../config/config.php' => config_path('airlines.php')]);

        // Append the country settings
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php', 'airlines'
        );
        /*$this->app['config']['database.connections'] = array_merge(
            $this->app['config']['database.connections'],
            \Config::get('career.library.database.connections')
        );*/
    }

    /**
     * Register everything.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAirlines();
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerAirlines()
    {
        $this->app->bind('airlines', function($app)
        {
            return new Airlines();
        });
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->singleton('command.airports.migration', function ($app) {
            return new MigrationCommand($app);
        });
        $this->commands('command.airports.migration');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('airlines');
    }
}
