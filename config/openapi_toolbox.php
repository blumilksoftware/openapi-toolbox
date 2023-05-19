<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\Config\Format;

return [
    "directory" => [
        "path" => resource_path("openapi"),
        "index" => "openapi.yml",
        "allow_multiple_files" => false,
    ],
    "format" => Format::Yml,
    "routing" => [
        "prefix" => "documentation",
        "name" => "documentation",
    ],
    "ui" => [
        "title" => "Documentation",
    ],
];
