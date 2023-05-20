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
    public function testMultipleFilesDocumentationValidation(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/multipleFilesDocumentation"));
        $config->set("openapi_toolbox.directory.allow_multiple_files", true);
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

    public function testMultipleInvalidFilesDocumentationValidation(): void
    {
        $config = new Repository();
        $config->set("openapi_toolbox.directory.index", "openapi.yml");
        $config->set("openapi_toolbox.directory.path", realpath(__DIR__ . "/mocks/multipleInvalidFilesDocumentation"));
        $config->set("openapi_toolbox.directory.allow_multiple_files", true);
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
}
