<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiCompatibility\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\ConfigHelper;
use Illuminate\Contracts\Config\Repository;
use KrzysztofRewak\OpenApiMerge\FileHandling\File;
use KrzysztofRewak\OpenApiMerge\OpenApiMerge;
use KrzysztofRewak\OpenApiMerge\Reader\FileReader;
use KrzysztofRewak\OpenApiMerge\Writer\DefinitionWriter;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class MultipleFilesDocumentationBuilder implements DocumentationBuilder
{
    public function __construct(
        protected Repository $config,
        protected ConfigHelper $configHelper,
    ) {}

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
        return $this->configHelper->getPath("*." . $this->config->get("openapi_toolbox.format")->value);
    }
}
