<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OpenApiBuilderTest extends TestCase
{
    /**
     * @throws InvalidFileTypeException
     */
    public function testSingleFileDocumentationBuild(): void
    {
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.yml",
                "path" => realpath(__DIR__ . "/mocks/yml/singleFileDocumentation"),
                "allow_multiple_files" => false,
            ],
            "format" => Format::Yml,
        ]);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationWithSingleFileConfigurationBuild(): void
    {
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.yml",
                "path" => realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"),
                "allow_multiple_files" => false,
            ],
            "format" => Format::Yml,
        ]);

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
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.yml",
                "path" => realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"),
                "allow_multiple_files" => true,
            ],
            "format" => Format::Yml,
        ]);

        $builder = new SpecificationBuilder($config);
        $result = $builder->build();

        Assert::assertStringContainsString("API Documentation", $result);
        Assert::assertStringContainsString("Ping endpoint", $result);
    }
}
