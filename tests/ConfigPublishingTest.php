<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Blumilk\OpenApiToolbox\OpenApiToolboxServiceProvider;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ConfigPublishingTest extends TestCase
{
    public function testConfigIsPublishedWithTag(): void
    {
        $paths = ServiceProvider::pathsToPublish(OpenApiToolboxServiceProvider::class, "openapi-toolbox-config");

        $this->assertCount(1, $paths);
        $this->assertFileExists(array_key_first($paths));
        $this->assertSame(config_path("openapi_toolbox.php"), reset($paths));
    }

    public function testConfigIsPublishedWithProvider(): void
    {
        $paths = ServiceProvider::pathsToPublish(OpenApiToolboxServiceProvider::class);

        $this->assertContains(config_path("openapi_toolbox.php"), $paths);
    }

    protected function getPackageProviders($app): array
    {
        return [
            OpenApiToolboxServiceProvider::class,
        ];
    }
}
