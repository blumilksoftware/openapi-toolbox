<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders;

interface DocumentationBuilder
{
    public function build(): string;
}
