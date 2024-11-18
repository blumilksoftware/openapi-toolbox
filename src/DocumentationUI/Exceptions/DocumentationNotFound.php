<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\DocumentationUI\Exceptions;

use Exception;

class DocumentationNotFound extends Exception
{
    public function __construct(string $documentation)
    {
        parent::__construct("Documentation named $documentation not found.");
    }
}
