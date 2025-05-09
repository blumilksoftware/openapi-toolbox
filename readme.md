[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/blumilksoftware/openapi-toolbox?style=for-the-badge)](https://packagist.org/packages/blumilksoftware/openapi-toolbox)
[![Packagist Version](https://img.shields.io/packagist/v/blumilksoftware/openapi-toolbox?style=for-the-badge)](https://packagist.org/packages/blumilksoftware/openapi-toolbox)
[![Packagist Downloads](https://img.shields.io/packagist/dt/blumilksoftware/openapi-toolbox?style=for-the-badge)](https://packagist.org/packages/blumilksoftware/openapi-toolbox/stats)

## 🧰 openapi-toolbox

OpenAPI Toolbox is a handy package with all important documentation-related features we are using in some of **[@blumilksoftware](https://github.com/blumilksoftware)** projects.

### Installation

Install package via Composer and publish configuration file:

```
composer require blumilksoftware/openapi-toolbox
php artisan vendor:publish
```

If you need it only for internal development (no documentation serving) you can install it with a development flag:

```
composer require blumilksoftware/openapi-toolbox --dev
php artisan vendor:publish
```

### Configuration

Configuration file should be published into your application after running `php artisan vendor:publish` command. It should look like below:

```php
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
```

### Features

#### Multiple documentations

You can create more than one documentation by adding element to `openapi_toolbox.documentations` array. 

```php

return {
    "test" => [
        "format" => Format::YmlToJson,
        "specification" => [
            "path" => resource_path("documentation/test"),
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
            "title" => "Test documentation",
            "routing" => [
                "prefix" => "documentation/test",
                "name" => "documentation.test",
                "middlewares" => [],
            ],
            "provider" => UIProvider::Elements,
        ],
    ],
}
```

#### OpenAPI documentation UI

With configuration `ui.enabled = true` a documentation UI will be built from configurable path and served on configurable route. Currently, the [Stoplight Elements](https://stoplight.io/open-source/elements) and [Swagger UI](https://swagger.io/tools/swagger-ui/) are only available UI base components configurable by `ui.provider` setting.

By default, it should be one documentation available under `GET /documentation`.

By changing configuration variable `ui.single_source` to `true`, application will serve already built single source file for GUI.

#### OpenAPI documentation endpoint

Serving a documentation itself can be tricky, especially if specification is built from multiple nested files. Here OpenAPI specification files will be built accordingly to configuration and by default the result should be available under `GET /documentation/raw`.

#### OpenAPI specification validation

OpenAPI specification files will be built accordingly to configuration and validated on demand by running an Artisan command:

```
php artisan openapi:validate {documentation}
```

Good example of usage would be adding this command to CI pipeline for opened pull requests.

#### Testing requests and responses against OpenAPI specification

Based on [kirschbaum-development/laravel-openapi-validator](https://github.com/kirschbaum-development/laravel-openapi-validator), special trait added to selected PHPUnit test cases enables validation against application's OpenAPI specification:

```
use \Blumilk\OpenApiToolbox\OpenApiCompatibility\OpenApiCompatibility;
```

By default, the documentation will be used from `openapi_toolbox.default` option. You should consider to override `getDocumentationName()` or `getDocumentationConfig()` method to select documentation you want to test. 

Every time any HTTP call to application would be performed during tests, additional validation will be performed and structure of requests and responses will be checked against OpenAPI specification. For special cases (e.g. testing invalid requests) this validation can be disabled by using `$this->withoutRequestValidation()` and `$this->withoutResponseValidation()`.  

With configuration `cache.enabled = true` internal builders will use cached OpenAPI specifications.
You can change the cache store by setting the `cache.driver` option, for example: `cache.driver = redis`. Make sure to use one of the cache drivers available in your Laravel application.
