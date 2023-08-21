<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use Illuminate\Config\Repository;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OpenApiCompatibilityTest extends TestCase
{
    /**
     * @throws InvalidFileTypeException
     * @throws UnresolvableReferenceException
     */
    public function testSingleFileDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.yml");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/yml/singleFileDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", false);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
    }

    /**
     * @throws InvalidFileTypeException
     * @throws UnresolvableReferenceException
     */
    public function testMultipleFilesDocumentationWithSingleFileConfigurationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.yml");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", false);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringNotContainsString("Ping endpoint", $result);
    }

    /**
     * @throws InvalidFileTypeException
     * @throws UnresolvableReferenceException
     */
    public function testMultipleFilesDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.yml");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringContainsString("Ping endpoint", $result);
    }
}
