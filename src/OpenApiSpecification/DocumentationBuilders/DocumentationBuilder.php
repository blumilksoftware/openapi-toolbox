<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

interface DocumentationBuilder
{
    public function build(): string;
}
