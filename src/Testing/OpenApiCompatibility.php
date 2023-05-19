<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Testing;

use Kirschbaum\OpenApiValidator\ValidatesOpenApiSpec;
use KrzysztofRewak\OpenApiMerge\FileHandling\File;
use KrzysztofRewak\OpenApiMerge\OpenApiMerge;
use KrzysztofRewak\OpenApiMerge\Reader\FileReader;
use KrzysztofRewak\OpenApiMerge\Writer\DefinitionWriter;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

trait OpenApiCompatibility
{
    use ValidatesOpenApiSpec;

    /**
     * @throws InvalidFileTypeException
     */
    protected function getOpenApiSpec(): string
    {
        return match (config("openapi_toolbox.allow_multiple_files")) {
            true => $this->getOpenApiSpecFromMultipleFile(),
            default => $this->getOpenApiSpecFromSingleFile(),
        };
    }

    protected function getOpenApiSpecFromSingleFile(): string
    {
        return file_get_contents(config("openapi_toolbox.index"));
    }

    /**
     * @throws InvalidFileTypeException
     */
    protected function getOpenApiSpecFromMultipleFile(): string
    {
        $merger = new OpenApiMerge(new FileReader());
        $mergedResult = $merger->mergeFiles(
            new File(config("openapi_toolbox.index")),
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
        return config("openapi_toolbox.path") . "/*." . config("openapi_toolbox.format");
    }
}
