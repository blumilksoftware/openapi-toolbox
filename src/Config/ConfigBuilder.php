<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Config;

class ConfigBuilder
{
    public static function build(
        ?string $documentationFilesDirectoryPath = null,
        ?string $documentationFilesIndex = "openapi.yml",
        bool $allowMultipleFiles = false,
        ?Format $format = null,
    ): array {
        $documentationFilesDirectoryPath ??= resource_path("openapi");
        $format ??= static::guessFormat($documentationFilesIndex)->value;

        return [
            "documentation_files_directory" => [
                "path" => $documentationFilesDirectoryPath,
                "index" => $documentationFilesDirectoryPath . "/" . $documentationFilesIndex,
                "allow_multiple_files" => $allowMultipleFiles,
            ],
            "format" => $format,
        ];
    }

    protected static function guessFormat(?string $documentationFilesIndex): Format
    {
        if (str_ends_with($documentationFilesIndex, ".json")) {
            return Format::Json;
        }

        if (str_ends_with($documentationFilesIndex, ".yaml")) {
            return Format::Yaml;
        }

        return Format::Yml;
    }
}
