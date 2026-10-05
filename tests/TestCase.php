<?php

namespace FLAIRUK\Airlines\Tests;

use FLAIRUK\Airlines\AirlinesServiceProvider;
use FLAIRUK\Airlines\Facades\Airlines;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AirlinesServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Airlines' => Airlines::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
    }
}
