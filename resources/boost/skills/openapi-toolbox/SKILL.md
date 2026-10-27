---
name: openapi-toolbox
description: Work with OpenAPI specification in a Laravel application using blumilksoftware/openapi-toolbox. Use when adding or changing API endpoints, editing OpenAPI files, writing feature tests validated against the specification, adding another documentation, or enabling the documentation UI or cache.
---

# OpenAPI Toolbox

`blumilksoftware/openapi-toolbox` serves, validates and tests a Laravel application against its OpenAPI specification files. Configuration lives in `config/openapi_toolbox.php`.

## Before you start

1. Read `config/openapi_toolbox.php` and note for the documentation you work on: `format`, `specification.path`, `specification.index`, `specification.allow_multiple_files`.
2. If the config file does not exist, publish it:
   ```
   php artisan vendor:publish --tag=openapi-toolbox-config
   ```
3. Open the index file (`specification.path`/`specification.index`) to learn the existing structure, naming of schemas and tags before adding anything.

## Configuration reference

| Key | Meaning |
| --- | --- |
| `format` | `Format::Yml`, `Format::Yaml`, `Format::Json` or `Format::YmlToJson` (YAML files served as JSON). Enum `Blumilk\OpenApiToolbox\Config\Format`. |
| `specification.path` | Directory with specification files. |
| `specification.index` | Entry file name inside `path`. |
| `specification.allow_multiple_files` | Merge the index with sibling files referenced by `$ref`. |
| `cache.enabled` / `cache.driver` / `cache.key` | Cache the built specification; `driver` is a Laravel cache store name or `default`. |
| `ui.enabled` | Register UI routes. |
| `ui.single_source` | `true`: UI loads the built specification from `/raw`; `false`: UI loads the index file and resolves `$ref` itself. |
| `ui.provider` | `UIProvider::Elements` or `UIProvider::Swagger` (enum `Blumilk\OpenApiToolbox\DocumentationUI\UIProvider`). |
| `ui.routing.prefix` / `name` / `middlewares` | URL prefix, route name and middlewares of UI routes. |

## Recipe: add or change an API endpoint

1. **Specification first.** Describe the path, parameters, request body, every response status code and response schema in the specification.
   - Single file: add the path under `paths` in the index file.
   - Multiple files (`allow_multiple_files = true`): create a new file directly in `specification.path` (no subdirectories, extension matching `format`) holding the path item, and reference it from the index:
     ```yaml
     paths:
       "/ping":
         "$ref": ping.yml
     ```
2. **Validate the files:**
   ```
   php artisan openapi:validate
   ```
   Pass the documentation name as an argument when it is not the default one. A failure means the files cannot be parsed or merged; fix it before continuing.
3. **Write a feature test** using the compatibility trait:
   ```php
   use Blumilk\OpenApiToolbox\OpenApiCompatibility\OpenApiCompatibility;

   class PingTest extends TestCase
   {
       use OpenApiCompatibility;

       public function testPing(): void
       {
           $this->getJson("/api/ping")->assertOk();
       }
   }
   ```
   Every request and response in this test is validated against the specification.
4. **Implement** the route, controller, request and resource until the test passes.
5. If validation fails, decide which side is wrong. The specification is the contract: change it only when the intended API really differs.

## Recipe: test invalid input or undocumented responses

The trait validates requests too, so a deliberately invalid payload fails before reaching the application. Disable validation only for that test:

```php
public function testValidationError(): void
{
    $this->withoutRequestValidation();

    $this->postJson("/api/login", [])->assertUnprocessable();
}
```

Use `$this->withoutResponseValidation()` when the response is intentionally not described in the specification. Prefer documenting error responses (e.g. `422`) over disabling response validation.

## Recipe: test against a non-default documentation

```php
public function getDocumentationName(): string
{
    return "admin";
}
```

Override `getDocumentationConfig(): DocumentationConfig` instead when the test needs a configuration that is not in `config/openapi_toolbox.php`.

## Recipe: add another documentation

1. Copy the `openapi` entry under `documentations` in `config/openapi_toolbox.php` and give it a new key, e.g. `admin`.
2. Change `specification.path` to a separate directory (e.g. `resource_path("openapi/admin")`) and create the index file there.
3. Give it unique `ui.routing.prefix` and `ui.routing.name` (e.g. `documentation/admin` and `documentation.admin`) and a unique `cache.key`, otherwise routes and cache entries collide.
4. Run `php artisan openapi:validate admin`.

## Recipe: expose the documentation UI

1. Set `ui.enabled = true` and choose `ui.provider`.
2. Protect it if needed with `ui.routing.middlewares` (e.g. `["auth"]`).
3. Check `php artisan route:list --name=documentation`; you should see the UI route, `.raw` and `.file`.
4. With multiple files, set `ui.single_source = true` so the UI receives one merged specification.

## Recipe: run validation in CI

Add `php artisan openapi:validate` (one call per documentation) to the pull request pipeline next to the test suite.
