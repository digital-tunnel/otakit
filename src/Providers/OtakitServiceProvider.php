<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Providers;

use Illuminate\Support\ServiceProvider;

class OtakitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/otakit.php' => config_path('otakit.php'),
        ]);
    }
}
