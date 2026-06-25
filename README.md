# Laravel MCP Ops

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eloquage/laravel-mcp-ops.svg?style=flat-square)](https://packagist.org/packages/eloquage/laravel-mcp-ops)
[![GitHub Tests Action Status](https://github.com/eloquage/laravel-mcp-ops/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/laravel-mcp-ops/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eloquage/laravel-mcp-ops.svg?style=flat-square)](https://packagist.org/packages/eloquage/laravel-mcp-ops)

Aggregate MCP ops server for infrastructure health checks and auth status across installed integrations.

## Requirements

- PHP 8.4+
- `laravel/mcp` ^0.8

## Installation

```bash
composer require eloquage/laravel-mcp-ops
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=laravel-mcp-ops-config
```

## MCP registration

This package **auto-registers** MCP routes when `laravel/mcp` is installed:

- Local handle: `ops` (configurable via `MCP_OPS_LOCAL_HANDLE`)
- Web path: `/mcp/ops` (configurable via `MCP_OPS_WEB_PATH`)

Start the local MCP server:

```bash
php artisan mcp:start ops
```

### IDE config (`.cursor/mcp.json`)

```json
{
    "mcpServers": {
        "ops": {
            "command": "php",
            "args": ["artisan", "mcp:start", "ops"]
        }
    }
}
```

## MCP tools

| Tool           | Description                                                               |
| -------------- | ------------------------------------------------------------------------- |
| `health_check` | Infrastructure health when a `HealthCheckRunner` is bound in the host app |
| `auth_status`  | Aggregated OAuth / CLI auth health across installed `AuthStatusProvider`s |

## Optional integrations

| Package                             | `auth_status` section |
| ----------------------------------- | --------------------- |
| `eloquage/laravel-google-workspace` | Google OAuth          |
| `eloquage/laravel-notebooklm`       | NotebookLM CLI        |

Install the packages you need; their auth providers register automatically.

## Health checks

`health_check` delegates to `Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner`. Bind an implementation in your app:

```php
use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;

$this->app->bind(HealthCheckRunner::class, YourHealthCheckRunner::class);
```

When no runner is bound, the tool returns a JSON error explaining how to register one.

## Custom auth providers

Implement `Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider` and tag it in your service provider:

```php
$this->app->singleton(MyAuthStatusProvider::class);
$this->app->tag([MyAuthStatusProvider::class], 'mcp-ops.auth-providers');
```

## Configuration

| Key                              | Env                              | Default                   |
| -------------------------------- | -------------------------------- | ------------------------- |
| `mcp-ops.mcp.local_handle`       | `MCP_OPS_LOCAL_HANDLE`           | `ops`                     |
| `mcp-ops.mcp.web_path`           | `MCP_OPS_WEB_PATH`               | `/mcp/ops`                |
| `mcp-ops.remediation.google`     | `MCP_OPS_GOOGLE_REMEDIATION`     | `php artisan google:auth` |
| `mcp-ops.remediation.notebooklm` | `MCP_OPS_NOTEBOOKLM_REMEDIATION` | `nlm login`               |

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT. See [LICENSE.md](LICENSE.md).
