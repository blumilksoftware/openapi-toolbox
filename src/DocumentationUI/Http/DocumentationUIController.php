<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\DocumentationUI\Http;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\Exceptions\DocumentationNotFound;
use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class DocumentationUIController
{
    /**
     * @throws DocumentationNotFound
     */
    public function index(string $documentation, Repository $config, UrlGeneratorContract $url, Factory $view): View
    {
        $data = $config->get("openapi_toolbox.documentations.$documentation");

        if (empty($data)) {
            throw new DocumentationNotFound($documentation);
        }

        $documentationConfig = new DocumentationConfig($data);

        $route = !$documentationConfig->isUiSingleSource()
            ? $url->route(
                name: $documentationConfig->getRouteName() . ".file",
                parameters: $documentationConfig->getIndex(),
            )
            : $url->route(
                name: $documentationConfig->getRouteName() . ".raw",
            );

        $template = match ($documentationConfig->getUiProvider()) {
            UIProvider::Swagger => "swagger",
            default => "elements",
        };

        return $view->make("openapi_toolbox::$template")
            ->with("title", $documentationConfig->getUiTitle())
            ->with("route", $route)
            ->with("styleHref", $config->get("openapi_toolbox.providers.$template.stylesheet.href"))
            ->with("styleSri", $config->get("openapi_toolbox.providers.$template.stylesheet.sri"))
            ->with("scriptSrc", $config->get("openapi_toolbox.providers.$template.script.src"))
            ->with("scriptSri", $config->get("openapi_toolbox.providers.$template.script.sri"));
    }

    /**
     * @throws InvalidFileTypeException|DocumentationNotFound
     */
    public function raw(string $documentation, Repository $config): Response
    {
        $data = $config->get("openapi_toolbox.documentations.$documentation");

        if (empty($data)) {
            throw new DocumentationNotFound($documentation);
        }

        $documentationConfig = new DocumentationConfig($data);
        $builder = new SpecificationBuilder($documentationConfig);
        $content = $builder->build();

        return $this->respondWithSpecification($content, $documentationConfig->getFormat());
    }

    /**
     * @throws DocumentationNotFound
     */
    public function file(string $documentation, string $filePath, Repository $config): Response
    {
        $data = $config->get("openapi_toolbox.documentations.$documentation");

        if (empty($data)) {
            throw new DocumentationNotFound($documentation);
        }

        $documentationConfig = new DocumentationConfig($data);

        $filePath = $documentationConfig->getPath($filePath);
        $content = file_get_contents($filePath);

        return $this->respondWithSpecification($content, $documentationConfig->getFormat());
    }

    protected function respondWithSpecification(string $content, Format $format): Response
    {
        return match (true) {
            $format->isYml() => new Response($content, headers: ["Content-Type" => "application/x-yaml"]),
            $format === Format::YmlToJson => new JsonResponse(Yaml::parse($content)),
            default => new JsonResponse(json_decode($content, associative: true)),
        };
    }
}
