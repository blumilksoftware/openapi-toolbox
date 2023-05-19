<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility;

use Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders\MultipleFilesDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders\SingleFileDocumentationBuilder;
use Illuminate\Contracts\Config\Repository;
use Kirschbaum\OpenApiValidator\ValidatesOpenApiSpec;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

trait OpenApiCompatibility
{
    use ValidatesOpenApiSpec;

    /**
     * @throws InvalidFileTypeException
     */
    protected function getOpenApiSpec(): string
    {
        /** @var Repository $config */
        $config = app("config");

        return match ($config->get("openapi_toolbox.allow_multiple_files")) {
            true => (new MultipleFilesDocumentationBuilder($config))->build(),
            default => (new SingleFileDocumentationBuilder($config))->build(),
        };
    }
}
