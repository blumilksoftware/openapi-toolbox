<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;

class SingleFileDocumentationBuilder implements DocumentationBuilder
{
    public function __construct(
        protected DocumentationConfig $config,
    ) {}

    public function build(): string
    {
        return file_get_contents($this->config->getIndex());
    }
}
