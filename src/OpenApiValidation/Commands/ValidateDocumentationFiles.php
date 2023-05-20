<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiValidation\Commands;

use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesValidator;
use Illuminate\Console\Command;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;

class ValidateDocumentationFiles extends Command
{
    protected $signature = "openapi:validate";
    protected $description = "Validate OpenAPI documentation files.";

    /**
     * @throws InvalidFileTypeException
     */
    public function handle(DocumentationFilesValidator $validator): void
    {
        $validator->validate();
    }
}
