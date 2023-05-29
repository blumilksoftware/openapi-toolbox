<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification;

use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\MultipleFilesDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\SingleFileDocumentationBuilder;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use Illuminate\Contracts\Config\Repository;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class SpecificationBuilder
{
    public function __construct(
        protected Repository $config,
    ) {}

    /**
     * @throws InvalidFileTypeException
     * @throws UnresolvableReferenceException
     */
    public function build(): string
    {
        return match ($this->config->get("openapi_toolbox.specification.allow_multiple_files")) {
            true => (new MultipleFilesDocumentationBuilder($this->config))->build(),
            default => (new SingleFileDocumentationBuilder($this->config))->build(),
        };
    }
}
