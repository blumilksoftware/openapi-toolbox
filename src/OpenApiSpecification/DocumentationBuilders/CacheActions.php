<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

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
        return $this->getCacheDriver()->has($this->config->getCacheKey())
            && $this->getCacheDriver()->has($this->config->getCacheChecksumKey());
    }

    protected function cacheChecksumIsValid(): bool
    {
        $cachedChecksum = $this->getCachedChecksum();
        $checksum = $this->getDocumentationChecksum();

        return $cachedChecksum === $checksum;
    }

    protected function getCachedDocumentation(): string
    {
        return $this->getCacheDriver()->get($this->config->getCacheKey());
    }

    protected function getCachedChecksum(): string
    {
        return $this->getCacheDriver()->get($this->config->getCacheChecksumKey());
    }

    protected function cacheDocumentation(string $content): void
    {
        $this->getCacheDriver()->put($this->config->getCacheKey(), $content);
        $this->getCacheDriver()->put($this->config->getCacheChecksumKey(), $this->getDocumentationChecksum());
    }

    protected function getDocumentationChecksum(): string
    {
        $documentationPath = $this->config->getPath();

        $process = Process::fromShellCommandline("find $documentationPath -type f -exec md5sum {} \;");
        $process->run();

        return $process->getOutput();
    }

    protected function getCacheDriver(): Repository
    {
        $store = $this->config->getCacheDriver();

        return $store === "default" ? Cache::driver() : Cache::driver($store);
    }
}
