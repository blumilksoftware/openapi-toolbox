<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;
use Blumilk\OpenApiToolbox\OpenApiToolboxServiceProvider;
use Blumilk\OpenApiToolbox\Tests\Stubs\DenyAccess;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class DocumentationUITest extends TestCase
{
    public function testRoutesAreRegisteredOnlyForDocumentationsWithEnabledUi(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $routes = $router->getRoutes();

        $this->assertTrue($routes->hasNamedRoute("documentation"));
        $this->assertTrue($routes->hasNamedRoute("documentation.raw"));
        $this->assertTrue($routes->hasNamedRoute("documentation.file"));
        $this->assertTrue($routes->hasNamedRoute("documentation.swagger"));
        $this->assertFalse($routes->hasNamedRoute("documentation.disabled"));
    }

    public function testCatchAllFileRouteDoesNotShadowRoutesOfDocumentationWithNestedPrefix(): void
    {
        $this->assertSame("documentation.swagger", Route::getRoutes()->match(Request::create("documentation/swagger"))->getName());
        $this->assertSame("documentation.swagger.raw", Route::getRoutes()->match(Request::create("documentation/swagger/raw"))->getName());
        $this->assertSame("documentation.swagger.file", Route::getRoutes()->match(Request::create("documentation/swagger/ping.yml"))->getName());
        $this->assertSame("documentation.file", Route::getRoutes()->match(Request::create("documentation/openapi.yml"))->getName());
    }

    public function testElementsUiIsServedWithSingleSource(): void
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $this->get("documentation")
            ->assertOk()
            ->assertSee("<title>Elements documentation</title>", escape: false)
            ->assertSee("<elements-api", escape: false)
            ->assertSee('apiDescriptionUrl="' . route("documentation.raw") . '"', escape: false)
            ->assertSee($config->get("openapi_toolbox.providers.elements.script.src"))
            ->assertSee($config->get("openapi_toolbox.providers.elements.script.sri"))
            ->assertSee($config->get("openapi_toolbox.providers.elements.stylesheet.href"))
            ->assertSee($config->get("openapi_toolbox.providers.elements.stylesheet.sri"));
    }

    public function testSwaggerUiIsServedWithIndexFile(): void
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $this->get("documentation/swagger")
            ->assertOk()
            ->assertSee("<title>Swagger documentation</title>", escape: false)
            ->assertSee("SwaggerUIBundle")
            ->assertSee('url: "' . route("documentation.swagger.file", "openapi.yml") . '"', escape: false)
            ->assertSee($config->get("openapi_toolbox.providers.swagger.script.src"))
            ->assertSee($config->get("openapi_toolbox.providers.swagger.script.sri"))
            ->assertSee($config->get("openapi_toolbox.providers.swagger.stylesheet.href"))
            ->assertSee($config->get("openapi_toolbox.providers.swagger.stylesheet.sri"));
    }

    public function testRawSpecificationIsConvertedFromYmlToJson(): void
    {
        $this->get("documentation/raw")
            ->assertOk()
            ->assertHeader("Content-Type", "application/json")
            ->assertJsonPath("info.title", "application")
            ->assertJsonPath("info.description", "API Documentation");
    }

    public function testRawSpecificationIsBuiltFromMultipleFiles(): void
    {
        $response = $this->get("documentation/swagger/raw")
            ->assertOk()
            ->assertHeader("Content-Type", "application/x-yaml");

        $this->assertStringContainsString("API Documentation", $response->getContent());
        $this->assertStringContainsString("Ping endpoint", $response->getContent());
    }

    public function testSpecificationFileIsServed(): void
    {
        $response = $this->get("documentation/swagger/ping.yml")
            ->assertOk()
            ->assertHeader("Content-Type", "application/x-yaml");

        $this->assertStringContainsString("Ping endpoint", $response->getContent());
        $this->assertStringNotContainsString("API Documentation", $response->getContent());
    }

    public function testRouteMiddlewaresAreApplied(): void
    {
        $this->get("documentation/protected")->assertForbidden();
        $this->get("documentation/protected/raw")->assertForbidden();
        $this->get("documentation/protected/openapi.yml")->assertForbidden();
    }

    public function testFilesOutsideSpecificationPathAreNotServed(): void
    {
        $this->get("documentation/../../../../composer.json")->assertNotFound();
        $this->get("documentation/..%2F..%2F..%2F..%2Fcomposer.json")->assertNotFound();
    }

    public function testMissingSpecificationFileIsNotFound(): void
    {
        $this->get("documentation/missing.yml")->assertNotFound();
    }

    public function testDisabledUiIsNotServed(): void
    {
        $this->get("documentation/disabled")->assertNotFound();
    }

    protected function getPackageProviders($app): array
    {
        return [
            OpenApiToolboxServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app["config"]->set("openapi_toolbox", require __DIR__ . "/../config/openapi_toolbox.php");
        $app["config"]->set("openapi_toolbox.documentations", [
            "openapi" => $this->documentation(
                format: Format::YmlToJson,
                path: __DIR__ . "/mocks/yml/singleFileDocumentation",
                title: "Elements documentation",
                prefix: "documentation",
                name: "documentation",
                provider: UIProvider::Elements,
                singleSource: true,
            ),
            "swagger" => $this->documentation(
                format: Format::Yml,
                path: __DIR__ . "/mocks/yml/multipleFilesDocumentation",
                title: "Swagger documentation",
                prefix: "documentation/swagger",
                name: "documentation.swagger",
                provider: UIProvider::Swagger,
                singleSource: false,
                multipleFiles: true,
            ),
            "protected" => $this->documentation(
                format: Format::Yml,
                path: __DIR__ . "/mocks/yml/singleFileDocumentation",
                title: "Protected documentation",
                prefix: "documentation/protected",
                name: "documentation.protected",
                provider: UIProvider::Elements,
                singleSource: true,
                middlewares: [DenyAccess::class],
            ),
            "disabled" => $this->documentation(
                format: Format::Yml,
                path: __DIR__ . "/mocks/yml/singleFileDocumentation",
                title: "Disabled documentation",
                prefix: "documentation/disabled",
                name: "documentation.disabled",
                provider: UIProvider::Elements,
                singleSource: true,
                enabled: false,
            ),
        ]);
    }

    protected function documentation(
        Format $format,
        string $path,
        string $title,
        string $prefix,
        string $name,
        UIProvider $provider,
        bool $singleSource,
        bool $multipleFiles = false,
        array $middlewares = [],
        bool $enabled = true,
    ): array {
        return [
            "format" => $format,
            "specification" => [
                "path" => realpath($path),
                "index" => "openapi.yml",
                "allow_multiple_files" => $multipleFiles,
            ],
            "cache" => [
                "enabled" => false,
                "driver" => "default",
                "key" => $name,
            ],
            "ui" => [
                "enabled" => $enabled,
                "single_source" => $singleSource,
                "title" => $title,
                "routing" => [
                    "prefix" => $prefix,
                    "name" => $name,
                    "middlewares" => $middlewares,
                ],
                "provider" => $provider,
            ],
        ];
    }
}
