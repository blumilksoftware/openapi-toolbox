<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\CachedMultipleFilesDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\CachedSingleFileDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\MultipleFilesDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders\SingleFileDocumentationBuilder;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class SpecificationBuilder
{
    public function __construct(
        protected DocumentationConfig $config,
    ) {}

    /**
     * @throws InvalidFileTypeException
     */
    public function build(): string
    {
        $cache = $this->config->isCacheEnabled();
        $multipleFiles = $this->config->allowsMultipleFiles();

        return match (true) {
            $cache && $multipleFiles => (new CachedMultipleFilesDocumentationBuilder($this->config))->build(),
            !$cache && $multipleFiles => (new MultipleFilesDocumentationBuilder($this->config))->build(),
            $cache && !$multipleFiles => (new CachedSingleFileDocumentationBuilder($this->config))->build(),
            default => (new SingleFileDocumentationBuilder($this->config))->build(),
        };
    }
}
