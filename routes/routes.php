<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\DocumentationUI\Http\DocumentationUIController;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;

/** @var Registrar $router */
$router = app()->make(Registrar::class);
/** @var Repository $config */
$config = app()->make(Repository::class);

if ($config->get("openapi_toolbox.ui.enabled")) {
    $prefix = $config->get("openapi_toolbox.ui.routing.prefix");
    $name = $config->get("openapi_toolbox.ui.routing.name");
    $middlewares = $config->get("openapi_toolbox.ui.routing.middlewares", default: []);

    $router->get("/$prefix/{filePath}", [DocumentationUIController::class, "file"])
        ->middleware($middlewares)
        ->where("filePath", ".*")
        ->name("$name.file");

    $router->get("/$prefix", [DocumentationUIController::class, "index"])
        ->middleware($middlewares)
        ->name("$name");
}
