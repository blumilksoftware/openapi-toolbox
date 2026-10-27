<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Tests\Stubs;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DenyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        return new Response("Access denied.", Response::HTTP_FORBIDDEN);
    }
}
