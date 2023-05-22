<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Format
    |--------------------------------------------------------------------------
    |
    | This option controls format of OpenApi specification. Case of enum
    | \Blumilk\OpenApiToolbox\Config\Format should be used here.
    |
    */
    "format" => Format::YmlToJson,
    "specification" => [
        "path" => resource_path("openapi"),
        "index" => "openapi.yml",
        "allow_multiple_files" => false,
    ],
    "ui" => [
        "enabled" => false,
        "provider" => UIProvider::Elements,
        "title" => "Documentation",
        "routing" => [
            "prefix" => "documentation",
            "name" => "documentation",
            "middlewares" => [],
        ],
    ],
];
