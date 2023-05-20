<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiValidation\Commands;

use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesInvalidException;
use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesValidator;
use Illuminate\Console\Command;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Symfony\Component\Console\Command\Command as BaseCommand;

class ValidateDocumentationFiles extends Command
{
    protected $signature = "openapi:validate";
    protected $description = "Validate OpenAPI documentation files.";

    /**
     * @throws InvalidFileTypeException
     */
    public function handle(DocumentationFilesValidator $validator): int
    {
        try {
            $validator->validate();
        } catch (DocumentationFilesInvalidException $exception) {
            $this->error($exception->getMessage());
            return BaseCommand::FAILURE;
        }

        $this->info("OpenAPI specification is formatted properly.");
        return BaseCommand::SUCCESS;
    }
}
