<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\DocumentationUI\Http;

use Blumilk\OpenApiToolbox\Config\Format;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Yaml\Yaml;

class DocumentationUIController
{
    public function index(Repository $config, UrlGeneratorContract $url, Factory $view): View
    {
        $route = $url->route(
            name: $config->get("openapi_toolbox.routing.name") . ".file",
            parameters: $config->get("openapi_toolbox.directory.index"),
        );

        return $view->make("openapi_toolbox::elements")
            ->with("title", $config->get("openapi_toolbox.ui.title"))
            ->with("route", $route);
    }

    public function file(Repository $config, string $filePath): JsonResponse
    {
        $filePath = $config->get("openapi_toolbox.directory.path") . "/" . $filePath;
        $content = file_get_contents($filePath);

        /** @var Format $format */
        $format = $config->get("openapi_toolbox.format");
        return match (true) {
            $format->isYml() => new JsonResponse(Yaml::parse($content)),
            default => new JsonResponse($content),
        };
    }
}
