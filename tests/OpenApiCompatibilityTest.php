<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiCompatibility\OpenApiCompatibility;
use Orchestra\Testbench\TestCase;

class OpenApiCompatibilityTest extends TestCase
{
    use OpenApiCompatibility;

    public function getDocumentationConfig(): DocumentationConfig
    {
        return new DocumentationConfig([
            "specification" => [
                "index" => "openapi.yml",
                "path" => realpath(__DIR__ . "/mocks/yml/loginDocumentation"),
                "allow_multiple_files" => false,
            ],
            "format" => Format::Yaml,
        ]);
    }

    public function testDocumentationCompatibility(): void
    {
        $response = $this->postJson("login", ["email" => "example@example.com", "password" => "password"]);
        $response->assertOk();
    }

    protected function defineRoutes($router): void
    {
        $router->post("login", fn() => ["status" => true]);
    }
}
