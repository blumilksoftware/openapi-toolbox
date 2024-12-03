<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Contracts\Config\Repository;
use Kirschbaum\OpenApiValidator\Exceptions\UnknownParserForFileTypeException;
use Kirschbaum\OpenApiValidator\Exceptions\UnknownSpecFileTypeException;
use Kirschbaum\OpenApiValidator\ValidatesOpenApiSpec;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

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

    protected function getSpecFileType(): string
    {
        return strtolower($this->getDocumentationConfig()->getFormat()->name);
    }

    public function getDocumentationConfig(): DocumentationConfig
    {
        /** @var Repository $config */
        $config = app("config");

        $name = $this->getDocumentationName();

        return new DocumentationConfig($config->get("openapi_toolbox.documentations.$name"));
    }

    public function getDocumentationName(): string
    {
        /** @var Repository $config */
        $config = app("config");

        return $config->get("openapi_toolbox.default");
    }

    /**
     * @throws InvalidFileTypeException
     */
    protected function getOpenApiSpec(): string
    {
        $builder = new SpecificationBuilder($this->getDocumentationConfig());

        return $builder->build();
    }
}
