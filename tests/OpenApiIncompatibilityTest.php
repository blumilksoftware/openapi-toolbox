<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests;

use Kirschbaum\OpenApiValidator\Exceptions\UnknownParserForFileTypeException;
use Kirschbaum\OpenApiValidator\Exceptions\UnknownSpecFileTypeException;
use League\OpenAPIValidation\PSR7\Exception\Validation\AddressValidationFailed;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OpenApiIncompatibilityTest extends OpenApiCompatibilityTest
{
    protected bool $failed = false;

    public function testDocumentationIncompatibility(): void
    {
        $this->postJson("login", ["email" => "example@example.com", "password" => "password"]);
        $this->assertTrue($this->failed);
    }

    protected function defineRoutes($router): void
    {
        $router->post("login", fn() => ["message" => "Unexpected message."]);
    }

    /**
     * @throws InvalidFileTypeException
     * @throws UnknownParserForFileTypeException
     * @throws UnknownSpecFileTypeException
     * @throws ValidationFailed
     */
    protected function validateResponse(OperationAddress $address, SymfonyResponse $response): void
    {
        $psr17Factory = new Psr17Factory();
        $psr = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        try {
            $this->getOpenApiValidatorBuilder()
                ->getResponseValidator()
                ->validate(
                    $address,
                    $psr->createResponse($response),
                );
        } catch (AddressValidationFailed) {
            $this->failed = true;
        }
    }
}
