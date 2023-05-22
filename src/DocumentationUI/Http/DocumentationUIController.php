<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\DocumentationUI\Http;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Symfony\Component\HttpFoundation\Response;

class DocumentationUIController
{
    public function index(Repository $config, UrlGeneratorContract $url, Factory $view): View
    {
        $route = $url->route(
            name: $config->get("openapi_toolbox.ui.routing.name") . ".file",
            parameters: $config->get("openapi_toolbox.specification.index"),
        );

        $template = match ($config->get("openapi_toolbox.ui.provider")) {
            UIProvider::Swagger => "swagger",
            default => "elements",
        };

        return $view->make("openapi_toolbox::$template")
            ->with("title", $config->get("openapi_toolbox.ui.title"))
            ->with("route", $route);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function raw(Repository $config): Response
    {
        $builder = new SpecificationBuilder($config);
        $content = $builder->build();

        /** @var Format $format */
        $format = $config->get("openapi_toolbox.format");

        return $this->respondWithSpecification($content, $format);
    }

    public function file(Repository $config, string $filePath): Response
    {
        $filePath = $config->get("openapi_toolbox.specification.path") . "/" . $filePath;
        $content = file_get_contents($filePath);

        /** @var Format $format */
        $format = $config->get("openapi_toolbox.format");

        return $this->respondWithSpecification($content, $format);
    }

    protected function respondWithSpecification(string $content, Format $format): Response {
        return match (true) {
            $format->isYml() => new Response($content, headers: ["Content-Type" => "application/x-yaml"]),
            default => new JsonResponse($content),
        };
    }
}
