<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\ConfigHelper;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders\MultipleFilesDocumentationBuilder;
use Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders\SingleFileDocumentationBuilder;
use Illuminate\Config\Repository;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OpenApiCompatibilityTest extends TestCase
{
    public function testSingleFileDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/singleFileDocumentation"));
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new SingleFileDocumentationBuilder(new ConfigHelper($config));
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.format", Format::Yml);

        $builder = new MultipleFilesDocumentationBuilder($config, new ConfigHelper($config));
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringContainsString("Ping endpoint", $result);
    }
}
