<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility;

use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
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
        $builder = new SpecificationBuilder($config);

        return $builder->build();
    }
}
