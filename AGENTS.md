# AGENTS.md

Laravel package `blumilksoftware/openapi-toolbox`: serves OpenAPI documentation UI, builds (merges) specification files, validates them with an Artisan command and validates HTTP requests/responses in tests.

## Commands

Run inside the Docker container (`make run`, then `make shell`) or locally with PHP matching `composer.json`:

- `composer test` - PHPUnit tests (`tests/`, Orchestra Testbench)
- `composer cs` - code style check (php-cs-fixer with `blumilksoftware/codestyle`, config in `codestyle.php`)
- `composer csf` - code style fix

CI (`.github/workflows/check.yml`) runs `composer validate`, `composer cs` and `composer test`.

## Structure

- `src/OpenApiToolboxServiceProvider.php` - registers the `openapi:validate` command, config publishing (tag `openapi-toolbox-config`), routes and views.
- `src/Config/DocumentationConfig.php` - typed accessors over one entry of `openapi_toolbox.documentations`; `Format` enum.
- `src/OpenApiSpecification/SpecificationBuilder.php` - picks one of four builders in `DocumentationBuilders/` (single/multiple files x cached/not cached). Caching lives in the `CacheActions` trait and is invalidated by an md5 checksum of the specification directory.
- `src/OpenApiValidation/` - `openapi:validate` command and validator (checks that files parse and merge).
- `src/OpenApiCompatibility/OpenApiCompatibility.php` - test trait on top of `kirschbaum-development/laravel-openapi-validator`.
- `src/DocumentationUI/` - controller and `UIProvider` enum; views in `resources/views`, routes in `routes/routes.php` (one set per documentation with `ui.enabled`).
- `config/openapi_toolbox.php` - published config; keep it in sync with the config example in `readme.md`.
- `resources/boost/` - guidelines and skill for Laravel Boost, shipped to applications using the package. Update them when public behaviour (config keys, command, trait methods, routes) changes.
- `tests/mocks/` - sample specifications (yml/json, single/multiple files, valid/invalid).

## Conventions

- `declare(strict_types=1);` in every PHP file, double quotes for strings, trailing commas in multiline lists.
- Use constructor property promotion and typed enums for config values.
- Public behaviour changes must be reflected in `readme.md` and `resources/boost/`.
