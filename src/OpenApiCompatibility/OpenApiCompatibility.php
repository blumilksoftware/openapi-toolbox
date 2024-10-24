<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility;

use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Contracts\Config\Repository;
use Kirschbaum\OpenApiValidator\Exceptions\UnknownParserForFileTypeException;
use Kirschbaum\OpenApiValidator\Exceptions\UnknownSpecFileTypeException;
use Kirschbaum\OpenApiValidator\ValidatesOpenApiSpec;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;

trait OpenApiCompatibility
{
    use ValidatesOpenApiSpec;

    /**
     * @throws InvalidFileTypeException
     * @throws UnknownParserForFileTypeException
     * @throws UnknownSpecFileTypeException
     */
    public function getOpenApiValidatorBuilder(): ValidatorBuilder
    {
        if (!isset($this->openApiValidatorBuilder)) {
            $specType = $this->getSpecFileType();

            if ($specType === "json") {
                $this->openApiValidatorBuilder = (new ValidatorBuilder())->fromJson($this->getOpenApiSpec());
            } elseif ($specType === "yaml") {
                $this->openApiValidatorBuilder = (new ValidatorBuilder())->fromYaml($this->getOpenApiSpec());
            } else {
                throw new UnknownParserForFileTypeException("Unknown parser for file type {$specType}");
            }
        }

        return $this->openApiValidatorBuilder;
    }

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
