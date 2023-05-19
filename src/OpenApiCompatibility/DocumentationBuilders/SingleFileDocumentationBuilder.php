<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders;

use Illuminate\Contracts\Config\Repository;

class SingleFileDocumentationBuilder implements DocumentationBuilder
{
    public function __construct(
        protected Repository $config,
    ) {}

    public function build(): string
    {
        return file_get_contents($this->config->get("openapi_toolbox.index"));
    }
}
