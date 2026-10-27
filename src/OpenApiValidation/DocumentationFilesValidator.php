<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiValidation;

use Blumilk\OpenApiToolbox\OpenApiSpecification\SpecificationBuilder;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use openapiphp\openapi\exceptions\UnresolvableReferenceException;
use Symfony\Component\Yaml\Exception\ParseException;
use TypeError;

class DocumentationFilesValidator
{
    public function __construct(
        protected SpecificationBuilder $specificationBuilder,
    ) {}

    /**
     * @throws InvalidFileTypeException
     * @throws DocumentationFilesInvalidException
     */
    public function validate(): void
    {
        try {
            $this->specificationBuilder->build();
        } catch (ParseException|TypeError) {
            throw new DocumentationFilesInvalidException();
        } catch (UnresolvableReferenceException $exception) {
            if ($exception->getPrevious() instanceof ParseException) {
                throw new DocumentationFilesInvalidException();
            }
        }
    }
}
