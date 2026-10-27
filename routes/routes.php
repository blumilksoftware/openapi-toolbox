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

$documentationsWithUi = array_filter(
    array_map(
        static fn(array $documentation): DocumentationConfig => new DocumentationConfig($documentation),
        $config->get("openapi_toolbox.documentations", []),
    ),
    static fn(DocumentationConfig $documentationConfig): bool => $documentationConfig->isUiEnabled(),
);

$registerFixedPathRoutes = static function (string $key, DocumentationConfig $documentationConfig) use ($router): void {
    $prefix = $documentationConfig->getRoutePrefix();
    $name = $documentationConfig->getRouteName();
    $middlewares = $documentationConfig->getRouteMiddlewares();

    $router->get("/$prefix/raw", [DocumentationUIController::class, "raw"])
        ->middleware($middlewares)
        ->name("$name.raw")
        ->defaults("documentation", $key);

    $router->get("/$prefix", [DocumentationUIController::class, "index"])
        ->middleware($middlewares)
        ->name("$name")
        ->defaults("documentation", $key);
};

$registerCatchAllFileRoute = static function (string $key, DocumentationConfig $documentationConfig) use ($router): void {
    $prefix = $documentationConfig->getRoutePrefix();
    $name = $documentationConfig->getRouteName();
    $middlewares = $documentationConfig->getRouteMiddlewares();

    $router->get("/$prefix/{filePath}", [DocumentationUIController::class, "file"])
        ->middleware($middlewares)
        ->where("filePath", ".*")
        ->name("$name.file")
        ->defaults("documentation", $key);
};

$documentationsWithMostNestedPrefixFirst = $documentationsWithUi;

uasort(
    $documentationsWithMostNestedPrefixFirst,
    static fn(DocumentationConfig $a, DocumentationConfig $b): int => strlen($b->getRoutePrefix()) <=> strlen($a->getRoutePrefix()),
);

foreach ($documentationsWithUi as $key => $documentationConfig) {
    $registerFixedPathRoutes($key, $documentationConfig);
}

foreach ($documentationsWithMostNestedPrefixFirst as $key => $documentationConfig) {
    $registerCatchAllFileRoute($key, $documentationConfig);
}
