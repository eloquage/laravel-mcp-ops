# Changelog

All notable changes to `laravel-mcp-ops` will be documented in this file.

## v1.0.4 - 2026-10-23

### Release readiness

- Simplify the README with install, server startup, tool, extension, and configuration examples.
- Add the package banner and clarify Laravel/PHP requirements.
- Remove private sibling packages as hard install dependencies; integrations remain optional.

Validated by 15 Pest tests (65 assertions), PHPStan, Pint, strict Composer validation and audit, 16 passing PHP 8.4–8.5 / Laravel 12–13 CI jobs, and a Laravel 12 consumer MCP initialize, tools/list, and tool-call smoke test.

## Unreleased

### Added

- `SpatieHealthCheckRunner` bridges `spatie/laravel-health` registered checks to the `health_check` MCP tool
- `mcp-ops.health.auto_register_spatie` config (env `MCP_OPS_HEALTH_AUTO_REGISTER_SPATIE`, default `true`)
- Auto-bind `HealthCheckRunner` when Spatie Health is installed and no custom runner is registered
