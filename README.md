# Laravel Airlines

[![Latest Stable Version](https://poser.pugx.org/ijeffro/laravel-airlines/v/stable)](https://packagist.org/packages/ijeffro/laravel-airlines)
[![Total Downloads](https://poser.pugx.org/ijeffro/laravel-airlines/downloads)](https://packagist.org/packages/ijeffro/laravel-airlines)
[![Latest Unstable Version](https://poser.pugx.org/ijeffro/laravel-airlines/v/unstable)](https://packagist.org/packages/ijeffro/laravel-airlines)
[![License](https://poser.pugx.org/ijeffro/laravel-airlines/license)](https://packagist.org/packages/ijeffro/laravel-airlines)

Laravel Airlines is a bundle for Laravel, providing Iata Code ISO 3166_3 and country codes for all the airlines.

**Please note that the dev-master version is for Laravel 5**

## Installation

Run `composer require ijeffro/laravel-airlines dev-master` in your Laravel root directory to install the latest version.

Or add `ijeffro/laravel-airlines` to `composer.json`.

    "ijeffro/laravel-airlines": "dev-master"

Run `composer update` to pull down the latest version of Airline List.

Edit `app/config/app.php` and add the `provider` and `filter`

    'providers' => [
        ijeffro\Airlines\AirlinesServiceProvider::class,
    ]

Now add the alias.

    'aliases' => [
        'Airlines' => ijeffro\Airlines\AirlinesFacade::class,
    ]


## Model

You can start by publishing the configuration. This is an optional step, it contains the table name and does not need to be altered. If the default name `airlines` suits you, leave it. Otherwise run the following command

    $ php artisan vendor:publish

Next generate the migration file:

    $ php artisan airlines:migration
    $ composer dump-autoload

It will generate the `<timestamp>_setup_airlines_table.php` migration and the `AirlinesSeeder.php` seeder. To make sure the data is seeded insert the following code in the `seeds/DatabaseSeeder.php`

    //Seed the airlines
    $this->call('AirlinesSeeder');
    $this->command->info('Seeded the airlines!');

You may now run it with the artisan migrate command:

    $ php artisan migrate --seed

After running this command the filled airlines table will be available
