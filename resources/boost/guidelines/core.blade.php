# Laravel MCP Ops

Aggregate MCP server for infrastructure health and integration auth status.

## MCP tools

| Tool | Contract |
|------|----------|
| `health_check` | `HealthCheckRunner` |
| `auth_status` | Tagged `mcp-ops.auth-providers` |

## Spatie Health bridge

When `spatie/laravel-health` is installed and `mcp-ops.health.auto_register_spatie` is `true`, `SpatieHealthCheckRunner` is bound automatically. Pair with `eloquage/laravel-document-archive-mcp` to surface archive pipeline checks.

Override with a custom `HealthCheckRunner` binding in the host app.

## Auth providers

Optional packages register tagged providers automatically:

- `eloquage/laravel-google-workspace` → `GoogleAuthStatusProvider`
- `eloquage/laravel-notebooklm` → `NotebookLmAuthStatusProvider`
