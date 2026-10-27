<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Illuminate\Support\Facades\Cache;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Orchestra\Testbench\TestCase;

class CachedDocumentationBuilderTest extends TestCase
{
    /**
     * @throws InvalidFileTypeException
     */
    public function testSingleFileDocumentationIsCached(): void
    {
        $result = (new SpecificationBuilder($this->config(multipleFiles: false)))->build();

        $this->assertStringContainsString("API Documentation", $result);
        $this->assertSame($result, Cache::get("openapi"));
        $this->assertNotEmpty(Cache::get("openapi.md5"));
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testMultipleFilesDocumentationIsCached(): void
    {
        $result = (new SpecificationBuilder($this->config(multipleFiles: true)))->build();

        $this->assertStringContainsString("Ping endpoint", $result);
        $this->assertSame($result, Cache::get("openapi"));
        $this->assertNotEmpty(Cache::get("openapi.md5"));
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testCachedDocumentationIsReturnedWhenChecksumIsValid(): void
    {
        $builder = new SpecificationBuilder($this->config(multipleFiles: true));
        $builder->build();

        Cache::put("openapi", "cached documentation");

        $this->assertSame("cached documentation", $builder->build());
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testDocumentationIsRebuiltWhenChecksumIsInvalid(): void
    {
        $builder = new SpecificationBuilder($this->config(multipleFiles: true));
        $builder->build();

        Cache::put("openapi", "stale documentation");
        Cache::put("openapi.md5", "outdated checksum");

        $result = $builder->build();

        $this->assertStringContainsString("Ping endpoint", $result);
        $this->assertSame($result, Cache::get("openapi"));
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function testConfiguredCacheDriverIsUsed(): void
    {
        $result = (new SpecificationBuilder($this->config(multipleFiles: false, driver: "secondary")))->build();

        $this->assertSame($result, Cache::driver("secondary")->get("openapi"));
        $this->assertFalse(Cache::driver()->has("openapi"));
    }

    protected function defineEnvironment($app): void
    {
        $app["config"]->set("cache.default", "array");
        $app["config"]->set("cache.stores.secondary", [
            "driver" => "array",
        ]);
    }

    protected function config(bool $multipleFiles, string $driver = "default"): DocumentationConfig
    {
        return new DocumentationConfig([
            "format" => Format::Yml,
            "specification" => [
                "path" => realpath(__DIR__ . "/mocks/yml/multipleFilesDocumentation"),
                "index" => "openapi.yml",
                "allow_multiple_files" => $multipleFiles,
            ],
            "cache" => [
                "enabled" => true,
                "driver" => $driver,
                "key" => "openapi",
            ],
        ]);
    }
}
