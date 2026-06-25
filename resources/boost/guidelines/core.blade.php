# Laravel MCP Ops

Aggregate MCP server for infrastructure health and auth status.

## Registration

Auto-registered as local handle `ops` (configurable via `mcp-ops.mcp.local_handle`).

```bash
php artisan mcp:start ops
```

## Tools

- `health_check` — requires a host-bound `Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner`
- `auth_status` — aggregates installed `AuthStatusProvider` implementations

## Optional integrations

- `eloquage/laravel-google-workspace` — Google OAuth section in `auth_status`
- `eloquage/laravel-notebooklm` — NotebookLM CLI section in `auth_status`

## Extending auth status

Tag custom providers with `mcp-ops.auth-providers` so `McpAuthStatusService` picks them up.
