<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox;

use Blumilk\OpenApiToolbox\OpenApiValidation\Commands\ValidateDocumentationFiles;
use Illuminate\Support\ServiceProvider;

class OpenApiToolboxServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ValidateDocumentationFiles::class,
            ]);
        }

        $this->publishes([
            __DIR__ . "/../config/openapi_toolbox.php" => config_path("openapi_toolbox.php"),
        ]);

        $this->loadRoutesFrom(__DIR__ . "/../routes/routes.php");
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "openapi_toolbox");
    }
}
