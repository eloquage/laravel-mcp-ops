# Changelog

All notable changes to `laravel-mcp-ops` will be documented in this file.

## Unreleased

### Added

- `SpatieHealthCheckRunner` bridges `spatie/laravel-health` registered checks to the `health_check` MCP tool
- `mcp-ops.health.auto_register_spatie` config (env `MCP_OPS_HEALTH_AUTO_REGISTER_SPATIE`, default `true`)
- Auto-bind `HealthCheckRunner` when Spatie Health is installed and no custom runner is registered
