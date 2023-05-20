<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\OpenApiValidation;

use Exception;

class DocumentationFilesInvalidException extends Exception
{
    protected $message = "OpenAPI Specification has not passed through validation.";
}
