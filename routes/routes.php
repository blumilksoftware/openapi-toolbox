<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\DocumentationUI\Controllers\DocumentationUIController;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;

$router = app()->make(Registrar::class);
$config = app()->make(Repository::class);

if ($config->get("openapi_toolbox.ui.enabled")) {
    $prefix = $config->get("openapi_toolbox.routing.prefix");
    $name = $config->get("openapi_toolbox.routing.name");

    if ($config->get(("openapi_toolbox.documentation_files_directory.allow_multiple_files"))) {
        $router->get("/$prefix/{filePath}", [DocumentationUIController::class, "file"])
            ->where("filePath", ".*")
            ->name("$name.file");
    } else {
        $router->get("/$prefix/{filePath}", [DocumentationUIController::class, "file"])
            ->name("$name.file");
    }

    $router->get("/$prefix", [DocumentationUIController::class, "index"])
        ->name("$name");
}
