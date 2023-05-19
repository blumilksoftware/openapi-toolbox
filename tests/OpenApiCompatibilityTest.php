<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

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
        $config->set("openapi_toolbox.index", realpath(__DIR__ . "/mocks/singleFileDocumentation/openapi.yml"));

        $builder = new SingleFileDocumentationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationBuild(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.index", realpath(__DIR__ . "/mocks/multipleFilesDocumentation/openapi.yml"));
        $config->set("openapi_toolbox.path", realpath(__DIR__ . "/mocks/multipleFilesDocumentation"));

        $builder = new MultipleFilesDocumentationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringContainsString("Ping endpoint", $result);
    }
}
