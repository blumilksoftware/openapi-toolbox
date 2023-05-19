<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\ConfigHelper;

class SingleFileDocumentationBuilder implements DocumentationBuilder
{
    public function __construct(
        protected ConfigHelper $configHelper,
    ) {}

    public function build(): string
    {
        return file_get_contents($this->configHelper->getIndex());
    }
}
