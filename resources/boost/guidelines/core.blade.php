## blumilksoftware/openapi-toolbox

This application keeps its OpenAPI specification as files and uses `blumilksoftware/openapi-toolbox` to serve, validate and test against it. The specification is the contract: change it first, then the code.

### Where things are
- Configuration: `config/openapi_toolbox.php`. Documentations are defined under `documentations`, the default one is named by `default` (usually `openapi`).
- Specification files: `specification.path` + `specification.index` of a documentation (by default `resource_path("openapi")` and `openapi.yml`).
- With `specification.allow_multiple_files = true` the index file references sibling files via `$ref` (e.g. `"/ping": { "$ref": ping.yml }`). Only files placed directly in `specification.path` (no subdirectories) with the extension matching `format` are merged (`.yml` for `Format::Yml` and `Format::YmlToJson`, `.yaml` for `Format::Yaml`, `.json` for `Format::Json`).

### Rules
- After every change in specification files run `php artisan openapi:validate` (or `php artisan openapi:validate <documentation>` for a non-default one). It checks that the files parse and merge; it does not replace request/response validation in tests.
- Feature tests that hit HTTP endpoints should use the `Blumilk\OpenApiToolbox\OpenApiCompatibility\OpenApiCompatibility` trait. Every request and response made in such a test is validated against the specification, so an undocumented endpoint, field or status code fails the test.
- When a test intentionally sends an invalid request or expects an undocumented response, call `$this->withoutRequestValidation()` and/or `$this->withoutResponseValidation()` before the request. Do not disable validation just to make a test pass; fix the specification or the code instead.
- To test against a documentation other than the default one, override `getDocumentationName(): string` in the test case.
- When `ui.enabled = true`, the UI is served at `GET /{ui.routing.prefix}` and the built specification at `GET /{ui.routing.prefix}/raw` (route names `{ui.routing.name}` and `{ui.routing.name}.raw`).
- When `cache.enabled = true`, the built specification is cached and invalidated automatically by a checksum of files in `specification.path`; no manual cache clearing is needed after editing the specification.
