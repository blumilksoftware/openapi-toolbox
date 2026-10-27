<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\Exceptions\DocumentationNotFound;
use Blumilk\OpenApiToolbox\OpenApiToolboxServiceProvider;
use Orchestra\Testbench\TestCase;

class ValidateDocumentationFilesCommandTest extends TestCase
{
    public function testDefaultDocumentationIsValidatedWithoutArgument(): void
    {
        $this->artisan("openapi:validate")
            ->expectsOutput("OpenAPI specification named openapi is formatted properly.")
            ->assertSuccessful();
    }

    public function testNamedDocumentationIsValidated(): void
    {
        $this->artisan("openapi:validate", ["documentation" => "multiple"])
            ->expectsOutput("OpenAPI specification named multiple is formatted properly.")
            ->assertSuccessful();
    }

    public function testInvalidDocumentationFails(): void
    {
        $this->artisan("openapi:validate", ["documentation" => "invalid"])
            ->assertFailed();
    }

    public function testUnknownDocumentationThrowsException(): void
    {
        $this->expectException(DocumentationNotFound::class);
        $this->expectExceptionMessageIs("Documentation named unknown not found.");

        $this->artisan("openapi:validate", ["documentation" => "unknown"])->run();
    }

    protected function getPackageProviders($app): array
    {
        return [
            OpenApiToolboxServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app["config"]->set("openapi_toolbox.default", "openapi");
        $app["config"]->set("openapi_toolbox.documentations", [
            "openapi" => $this->documentation(__DIR__ . "/mocks/yml/singleFileDocumentation", multipleFiles: false),
            "multiple" => $this->documentation(__DIR__ . "/mocks/yml/multipleFilesDocumentation", multipleFiles: true),
            "invalid" => $this->documentation(__DIR__ . "/mocks/yml/multipleInvalidFilesDocumentation", multipleFiles: true),
        ]);
    }

    protected function documentation(string $path, bool $multipleFiles): array
    {
        return [
            "format" => Format::Yml,
            "specification" => [
                "path" => realpath($path),
                "index" => "openapi.yml",
                "allow_multiple_files" => $multipleFiles,
            ],
        ];
    }
}
