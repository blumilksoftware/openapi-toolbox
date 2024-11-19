<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\DocumentationUI\Http\DocumentationUIController;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;

/** @var Registrar $router */
$router = app()->make(Registrar::class);
/** @var Repository $config */
$config = app()->make(Repository::class);

$documentations = $config->get("openapi_toolbox.documentations", []);

foreach ($documentations as $key => $documentation) {
    $documentationConfig = new DocumentationConfig($documentation);

    if (!$documentationConfig->isUiEnabled()) {
        continue;
    }

    $prefix = $documentationConfig->getRoutePrefix();
    $name = $documentationConfig->getRouteName();
    $middlewares = $documentationConfig->getRouteMddlewares();

    $router->get("/$prefix/raw", [DocumentationUIController::class, "raw"])
        ->middleware($middlewares)
        ->name("$name.raw")
        ->defaults("documentation", $key);

    $router->get("/$prefix/{filePath}", [DocumentationUIController::class, "file"])
        ->middleware($middlewares)
        ->where("filePath", ".*")
        ->name("$name.file")
        ->defaults("documentation", $key);

    $router->get("/$prefix", [DocumentationUIController::class, "index"])
        ->middleware($middlewares)
        ->name("$name")
        ->defaults("documentation", $key);
}
