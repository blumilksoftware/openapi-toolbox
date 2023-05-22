<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\Config\Format;

return [
    "specification" => [
        "path" => resource_path("openapi"),
        "index" => "openapi.yml",
        "allow_multiple_files" => false,
    ],
    "format" => Format::Yml,
    "ui" => [
        "enabled" => false,
        "title" => "Documentation",
        "routing" => [
            "prefix" => "documentation",
            "name" => "documentation",
            "middlewares" => [],
        ],
    ],
];
