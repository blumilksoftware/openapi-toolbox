<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiValidation\Commands;

use Blumilk\OpenApiToolbox\Config\DocumentationConfig;
use Blumilk\OpenApiToolbox\DocumentationUI\Exceptions\DocumentationNotFound;
use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesInvalidException;
use Blumilk\OpenApiToolbox\OpenApiValidation\DocumentationFilesValidator;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use KrzysztofRewak\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Symfony\Component\Console\Command\Command as BaseCommand;

class ValidateDocumentationFiles extends Command
{
    protected $signature = "openapi:validate {documentation?}";
    protected $description = "Validate OpenAPI documentation files.";

    /**
     * @throws DocumentationNotFound
     * @throws InvalidFileTypeException
     */
    public function handle(Repository $config): int
    {
        $documentation = $this->argument("documentation") ?? $config->get("openapi_toolbox.default");

        $data = $config->get("openapi_toolbox.documentations.$documentation");

        if (empty($data)) {
            throw new DocumentationNotFound($documentation);
        }

        try {
            $validator = new DocumentationFilesValidator(new SpecificationBuilder(new DocumentationConfig($data)));

            $validator->validate();
        } catch (DocumentationFilesInvalidException $exception) {
            $this->error($exception->getMessage());

            return BaseCommand::FAILURE;
        }

        $this->info("OpenAPI specification named $documentation is formatted properly.");

        return BaseCommand::SUCCESS;
    }
}
