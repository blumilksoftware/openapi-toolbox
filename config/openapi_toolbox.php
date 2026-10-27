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
                "driver" => "default",
                "key" => "openapi",
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
                "src" => "https://unpkg.com/@stoplight/elements@9.0.29/web-components.min.js",
                "sri" => "sha384-enyjBaVmV00WFIo0D7AIVgtZ/4x0idVNWHotG8WUpvQo9HXgMSGAAW2YEOpiJTpP",
            ],
            "stylesheet" => [
                "href" => "https://unpkg.com/@stoplight/elements@9.0.29/styles.min.css",
                "sri" => "sha384-iVQBHadsD+eV0M5+ubRCEVXrXEBj+BqcuwjUwPoVJc0Pb1fmrhYSAhL+BFProHdV",
            ],
        ],
        "swagger" => [
            "script" => [
                "src" => "https://unpkg.com/swagger-ui-dist@5.34.0/swagger-ui-bundle.js",
                "sri" => "sha384-9Td7MKiIr2S83E1kZyyVi8KdJw/S8CpAvu0llLyMeg0xq/hDGiZJfVn0fV4llCmK",
            ],
            "stylesheet" => [
                "href" => "https://unpkg.com/swagger-ui-dist@5.34.0/swagger-ui.css",
                "sri" => "sha384-4c3vsE5f+trhFyHrFrLM1fFGUn531NIaC7dcqEWIJdoRQJ5CkJbSuSCwXLpaS1KF",
            ],
        ],
    ],
];
