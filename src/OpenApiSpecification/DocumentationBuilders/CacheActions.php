<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Closure;

trait CacheActions
{
    protected function cache(Closure $closure): string
    {
        if ($this->cachedDocumentationExists()) {
            if ($this->cacheChecksumIsValid()) {
                return $this->getCachedDocumentation();
            }
        }

        $content = $closure->call($this);
        $this->cacheDocumentation($content);

        return $content;
    }

    protected function cachedDocumentationExists(): bool
    {
        return file_exists($this->config->get("openapi_toolbox.cache.documentation_path")) && file_exists($this->config->get("openapi_toolbox.cache.checksum_path"));
    }

    protected function getCachedDocumentation(): string
    {
        return file_get_contents($this->config->get("openapi_toolbox.cache.documentation_path"));
    }

    protected function cacheChecksumIsValid(): bool
    {
        $path = $this->config->get("openapi_toolbox.cache.checksum_path");
        $md5checksum = shell_exec("md5sum -c $path");

        return count(
            array_filter(
                explode("\n", $md5checksum),
                fn(string $line): bool => str_ends_with($line, ": FAILED"),
            ),
        ) === 0;
    }

    protected function cacheDocumentation(string $content): void
    {
        file_put_contents($this->config->get("openapi_toolbox.cache.documentation_path"), $content);

        $documentationPath = $this->config->get("openapi_toolbox.specification.path");
        $checksumPath = $this->config->get("openapi_toolbox.cache.checksum_path");
        shell_exec("find $documentationPath -type f -exec md5sum {} \; > $checksumPath");
    }
}
