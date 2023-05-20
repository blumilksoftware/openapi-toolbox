<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Config\Repository;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OpenApiCompatibilityTest extends TestCase
{
    /**
     * @throws InvalidFileTypeException
     */
    public function testSingleFileDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/singleFileDocumentation"));
        $config->set("openapi_toolbox.directory.allow_multiple_files", false);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationWithSingleFileConfigurationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.directory.allow_multiple_files", false);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringNotContainsString("Ping endpoint", $result);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.directory.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringContainsString("Ping endpoint", $result);
    }
}
