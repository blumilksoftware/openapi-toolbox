<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class CachedMultipleFilesDocumentationBuilder extends MultipleFilesDocumentationBuilder
{
    /**
     * @throws InvalidFileTypeException
     */
    public function build(): string
    {
        if ($this->cachedDocumentationExists()) {
            if ($this->cacheChecksumIsValid()) {
                return $this->getCachedDocumentation();
            }
        }

        $content = parent::build();
        $this->cacheDocumentation($content);

        return $content;
    }

    protected function cachedDocumentationExists(): bool
    {
        return file_exists($this->config->get("openapi_toolbox.cache.documentation_path"));
    }

    protected function getCachedDocumentation(): string
    {
        return file_get_contents($this->config->get("openapi_toolbox.cache.documentation_path"));
    }

    protected function cacheChecksumIsValid(): bool
    {
        $md5checksum = file_get_contents($this->config->get("openapi_toolbox.cache.checksum_path"));

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
        shell_exec("find $documentationPath -exec md5sum {} \; > $checksumPath");
    }
}
