<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

class CachedMultipleFilesDocumentationBuilder extends MultipleFilesDocumentationBuilder
{
    use CacheActions;

    public function build(): string
    {
        return $this->cache(fn() => parent::build());
    }
}
