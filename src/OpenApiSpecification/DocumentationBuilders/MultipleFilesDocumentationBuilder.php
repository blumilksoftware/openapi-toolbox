<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiSpecification\DocumentationBuilders;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\Config\Format;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\Merge\ComponentsMerger;
use Mthole\OpenApiMerge\Merge\ReferenceNormalizer;
use Mthole\OpenApiMerge\Merge\SecurityPathMerger;
use Mthole\OpenApiMerge\OpenApiMerge;
use Mthole\OpenApiMerge\Reader\FileReader;
use Mthole\OpenApiMerge\Writer\DefinitionWriter;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class MultipleFilesDocumentationBuilder implements DocumentationBuilder
{
    public function __construct(
        protected DocumentationConfig $config,
    ) {}

    /**
     * @throws InvalidFileTypeException
     */
    public function build(): string
    {
        $merger = new OpenApiMerge(
            new FileReader(),
            [
                new ComponentsMerger(),
                new SecurityPathMerger(),
            ],
            new ReferenceNormalizer(),
        );

        $mergedResult = $merger->mergeFiles(
            new File($this->config->getIndexPath()),
            array_map(
                static fn(string $file): File => new File($file),
                glob($this->getDocumentationFilesPathPattern()),
            ),
        );

        $writer = new DefinitionWriter();

        return $writer->write($mergedResult);
    }

    protected function getDocumentationFilesPathPattern(): string
    {
        $extension = match ($this->config->getFormat()) {
            Format::Yaml => "yaml",
            Format::Json => "json",
            default => "yml",
        };

        return $this->config->getPath("*.$extension");
    }
}
