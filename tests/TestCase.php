<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Tests;

use DigitalTunnel\Otakit\Providers\OtakitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            OtakitServiceProvider::class,
        ];
    }
}
