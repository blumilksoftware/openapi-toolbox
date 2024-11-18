<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesValidator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Throwable;

class OpenApiValidationTest extends TestCase
{
    public function testMultipleFilesYamlDocumentationValidation(): void
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
        $validator = new DocumentationFilesValidator($builder);

        $exceptions = [];

        try {
            $validator->validate();
        } catch (Throwable $e) {
            $exceptions[] = $e;
        }

        Assert::assertCount(0, $exceptions);
    }

    public function testMultipleInvalidFilesYamlDocumentationValidation(): void
    {
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.yml",
                "path" => realpath(__DIR__ . "/mocks/yml/multipleInvalidFilesDocumentation"),
                "allow_multiple_files" => true,
            ],
            "format" => Format::Yml,
        ]);

        $builder = new SpecificationBuilder($config);
        $validator = new DocumentationFilesValidator($builder);

        $exceptions = [];

        try {
            $validator->validate();
        } catch (Throwable $e) {
            $exceptions[] = $e;
        }

        Assert::assertCount(1, $exceptions);
    }

    public function testMultipleFilesJsonDocumentationValidation(): void
    {
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.json",
                "path" => realpath(__DIR__ . "/mocks/json/multipleFilesDocumentation"),
                "allow_multiple_files" => true,
            ],
            "format" => Format::Json,
        ]);

        $builder = new SpecificationBuilder($config);
        $validator = new DocumentationFilesValidator($builder);

        $exceptions = [];

        try {
            $validator->validate();
        } catch (Throwable $e) {
            $exceptions[] = $e;
        }

        Assert::assertCount(0, $exceptions);
    }

    /**
     * This test case is intentionally throwing a PHP warning. It is because we are testing invalid JSON files here.
     */
    public function testMultipleInvalidFilesJsonDocumentationValidation(): void
    {
        $config = new DocumentationConfig([
            "specification" => [
                "index" => "openapi.json",
                "path" => realpath(__DIR__ . "/mocks/json/multipleInvalidFilesDocumentation"),
                "allow_multiple_files" => true,
            ],
            "format" => Format::Json,
        ]);

        $builder = new SpecificationBuilder($config);
        $validator = new DocumentationFilesValidator($builder);

        $exceptions = [];

        try {
            $validator->validate();
        } catch (Throwable $e) {
            $exceptions[] = $e;
        }

        Assert::assertCount(1, $exceptions);
    }
}
