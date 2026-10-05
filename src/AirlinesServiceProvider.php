<?php

namespace FLAIRUK\Airlines;

use Illuminate\Support\ServiceProvider;

class AirlinesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/airlines.php', 'airlines');

        $this->app->singleton(Airlines::class);
        $this->app->alias(Airlines::class, 'airlines');
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/airlines.php' => config_path('airlines.php'),
        ], 'airlines-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'airlines-migrations');

        $this->commands([
            Console\InstallCommand::class,
            Console\SeedCommand::class,
        ]);
    }
}
