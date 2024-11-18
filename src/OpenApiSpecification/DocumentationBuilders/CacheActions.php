<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Closure;
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
        return file_exists($this->config->getCachePath()) && file_exists($this->config->getCacheChecksumPath());
    }

    protected function cacheChecksumIsValid(): bool
    {
        $path = $this->config->getCacheChecksumPath();
        $process = new Process(["md5sum", "-c", $path]);
        $process->run();

        return $process->isSuccessful();
    }

    protected function getCachedDocumentation(): string
    {
        return file_get_contents($this->config->getCachePath());
    }

    protected function cacheDocumentation(string $content): void
    {
        file_put_contents($this->config->getCachePath(), $content);

        $documentationPath = $this->config->getPath();
        $checksumPath = $this->config->getCacheChecksumPath();

        $process = Process::fromShellCommandline("find $documentationPath -type f -exec md5sum {} \; > $checksumPath");
        $process->run();
    }
}
