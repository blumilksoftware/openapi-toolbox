<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\ConfigHelper;
use Blumilk\OpenApiToolbox\Config\Format;
use Illuminate\Contracts\Config\Repository;
use KrzysztofRewak\OpenApiMerge\FileHandling\File;
use KrzysztofRewak\OpenApiMerge\OpenApiMerge;
use KrzysztofRewak\OpenApiMerge\Reader\FileReader;
use KrzysztofRewak\OpenApiMerge\Writer\DefinitionWriter;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class MultipleFilesDocumentationBuilder implements DocumentationBuilder
{
    protected ConfigHelper $configHelper;

    public function __construct(
        protected Repository $config,
    ) {
        $this->configHelper = new ConfigHelper($this->config);
    }

    /**
     * @throws InvalidFileTypeException
     */
    public function build(): string
    {
        $index = $this->configHelper->getIndex();

        $merger = new OpenApiMerge(new FileReader());
        $mergedResult = $merger->mergeFiles(
            new File($index),
            ...array_map(
                static fn(string $file): File => new File($file),
                glob($this->getDocumentationFilesPathPattern()),
            ),
        );

        $writer = new DefinitionWriter();

        return $writer->write($mergedResult);
    }

    protected function getDocumentationFilesPathPattern(): string
    {
        $extension = match ($this->config->get("openapi_toolbox.format")) {
            Format::Yaml => "yaml",
            Format::Json => "json",
            default => "yml",
        };

        return $this->configHelper->getPath("*.$extension");
    }
}
