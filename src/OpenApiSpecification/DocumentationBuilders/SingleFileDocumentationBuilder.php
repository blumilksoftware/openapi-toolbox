<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\ConfigHelper;
use Illuminate\Contracts\Config\Repository;

class SingleFileDocumentationBuilder implements DocumentationBuilder
{
    protected ConfigHelper $configHelper;

    public function __construct(Repository $config) {
        $this->configHelper = new ConfigHelper($config);
    }

    public function build(): string
    {
        return file_get_contents($this->configHelper->getIndex());
    }
}
