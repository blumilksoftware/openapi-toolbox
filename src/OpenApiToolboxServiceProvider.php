<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox;

use Illuminate\Support\ServiceProvider;

class OpenApiToolboxServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . "/../routes/routes.php");
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "openapi_toolbox");
    }
}
