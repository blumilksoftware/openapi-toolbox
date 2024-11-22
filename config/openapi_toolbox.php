<?php

declare(strict_types=1);

use Blumilk\OpenApiToolbox\Config\Format;
use Blumilk\OpenApiToolbox\DocumentationUI\UIProvider;

return [
    "default" => "openapi",
    "documentations" => [
        "openapi" => [
            "format" => Format::YmlToJson,
            "specification" => [
                "path" => resource_path("openapi"),
                "index" => "openapi.yml",
                "allow_multiple_files" => false,
            ],
            "cache" => [
                "enabled" => false,
                "documentation_path" => storage_path("framework/cache/openapi"),
                "checksum_path" => storage_path("framework/cache/openapi.md5"),
            ],
            "ui" => [
                "enabled" => false,
                "single_source" => false,
                "title" => "Documentation",
                "routing" => [
                    "prefix" => "documentation",
                    "name" => "documentation",
                    "middlewares" => [],
                ],
                "provider" => UIProvider::Elements,
            ],
        ],
    ],
    "providers" => [
        "elements" => [
            "script" => [
                "src" => "https://unpkg.com/@stoplight/elements@7.7.16/web-components.min.js",
                "sri" => "sha384-bwBnouovwwSJc5fWe7VFNxRg+T2lPHhUcHIzdf7mFfqTZkYtM3T/ehzfEr8F02yY",
            ],
            "stylesheet" => [
                "href" => "https://unpkg.com/@stoplight/elements@7.7.16/styles.min.css",
                "sri" => "sha384-1lLf7J28IOR7k5RlItk6Y+G3hDgVB3y4RCgWNq6ZSwjYfvJXPtZAdW0uklsAZbGW",
            ],
        ],
        "swagger" => [
            "script" => [
                "src" => "https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui-bundle.js",
                "sri" => "sha384-xy3YXp34ftsoHshRtcUFjOl/M22B5OEHD5S9AjtVzQokz+BxNff8vNW08msKmH46",
            ],
            "stylesheet" => [
                "href" => "https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui.css",
                "sri" => "sha384-pzdBB6iZwPIzBHgXle+9cgvKuMgtWNrBopXkjrWnKCi3m4uJsPPdLQ4IPMqRDirS",
            ],
        ],
    ],
];
