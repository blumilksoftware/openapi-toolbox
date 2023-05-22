## 🧰 openapi-toolbox

OpenAPI Toolbox is a handy package with all important documentation-related features we are using in some of **[@blumilksoftware](https://github.com/blumilksoftware)** projects.

### Installation

Install package via Composer:

```
composer require blumilksoftware/openapi-toolbox
```

If you need it only for internal development (no documentation serving) you can install it with a development flag:

```
composer require blumilksoftware/openapi-toolbox --dev
```

### Configuration

Configuration file should be published into your application after `vendor:publish` command. It should look like below:

```php
return [
    "format" => \Blumilk\OpenApiToolbox\Config\Format::Yml,
    "specification" => [
        "path" => resource_path("openapi"),
        "index" => "openapi.yml",
        "allow_multiple_files" => false,
    ],
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
```

### Features

#### OpenAPI documentation UI

With configuration `openapi_toolbox.ui.enabled = true` a documentation UI will be built from configurable path and served on configurable route. Currently, the [Stoplight Elements](https://stoplight.io/open-source/elements) is only available UI base component.

By default, it should be available under `GET /documentation`.

#### OpenAPI specification validation

OpenAPI specification files will be built accordingly to configuration and validated on demand by running an Artisan command:

```
php artisan openapi:validate
```

Good example of usage would be adding this command to CI pipeline for opened pull requests.

#### Testing requests and responses against OpenAPI specification

Based on [kirschbaum-development/laravel-openapi-validator](https://github.com/kirschbaum-development/laravel-openapi-validator), special trait added to selected PHPUnit test cases enables validation against application's OpenAPI specification:

```
use \Blumilk\OpenApiToolbox\OpenApiCompatibility\OpenApiCompatibility;
```

Every time any HTTP call to application would be performed during tests, additional validation will be performed and structure of requests and responses will be checked against OpenAPI specification. For special cases (e.g. testing invalid requests) this validation can be disabled by using `$this->withoutRequestValidation()` and `$this->withoutResponseValidation()`.  
