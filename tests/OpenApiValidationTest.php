<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesValidator;
use Illuminate\Config\Repository;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Throwable;

class OpenApiValidationTest extends TestCase
{
    public function testMultipleFilesYamlDocumentationValidation(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.yml");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Yml);

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
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.yml");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/yml/multipleInvalidFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Yml);

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
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.json");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/json/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Json);

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
        $config = new Repository();
        $config->set("openapi_toolbox.specification.index", "openapi.json");
        $config->set("openapi_toolbox.specification.path", realpath(__DIR__ . "/mocks/json/multipleInvalidFilesDocumentation"));
        $config->set("openapi_toolbox.specification.allow_multiple_files", true);
        $config->set("openapi_toolbox.format", Format::Json);

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
