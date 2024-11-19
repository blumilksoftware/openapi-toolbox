<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Config;

use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;
use Illuminate\Support\Arr;

class DocumentationConfig
{
    public function __construct(
        protected array $config,
    ) {}

    public function getPath(?string $file = null): string
    {
        $path = Arr::get($this->config, "specification.path");

        return !empty($file) ? "$path/$file" : $path;
    }

    public function getIndex(): string
    {
        return Arr::get($this->config, "specification.index");
    }

    public function getIndexPath(): string
    {
        return $this->getPath($this->getIndex());
    }

    public function isUiEnabled(): bool
    {
        return Arr::get($this->config, "ui.enabled", false);
    }

    public function isUiSingleSource(): bool
    {
        return Arr::get($this->config, "ui.single_source", true);
    }

    public function getUiTitle(): string
    {
        return Arr::get($this->config, "ui.title");
    }

    public function getUiProvider(): UIProvider
    {
        return Arr::get($this->config, "ui.provider");
    }

    public function getRoutePrefix(): string
    {
        return Arr::get($this->config, "ui.routing.prefix");
    }

    public function getRouteName(): string
    {
        return Arr::get($this->config, "ui.routing.name");
    }

    public function getRouteMddlewares(): array
    {
        return Arr::get($this->config, "ui.routing.middlewares", []);
    }

    public function allowsMultipleFiles(): bool
    {
        return Arr::get($this->config, "specification.allow_multiple_files", false);
    }

    public function isCacheEnabled(): bool
    {
        return Arr::get($this->config, "cache.enabled", false);
    }

    public function getCachePath(): string
    {
        return Arr::get($this->config, "cache.documentation_path");
    }

    public function getCacheChecksumPath(): string
    {
        return Arr::get($this->config, "cache.checksum_path");
    }

    public function getFormat(): Format
    {
        return Arr::get($this->config, "format");
    }
}
