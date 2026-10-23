# Laravel MCP Ops

![Laravel MCP Ops package banner](art/banner.png)

An aggregate Laravel MCP server for infrastructure health and authentication status across installed integrations.

[![Tests](https://github.com/eloquage/laravel-mcp-ops/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/laravel-mcp-ops/actions/workflows/run-tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/eloquage/laravel-mcp-ops)](https://packagist.org/packages/eloquage/laravel-mcp-ops)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)

Requires PHP 8.4+, Laravel 11, 12, or 13, and `laravel/mcp` 0.9 or later.

## Install

```bash
composer require eloquage/laravel-mcp-ops
php artisan vendor:publish --tag=mcp-ops-config
```

The package registers a local MCP server named `ops` and a web endpoint at `/mcp/ops`. Start the local server with:

```bash
php artisan mcp:start ops
```

## Tools

| Tool | What it reports |
| --- | --- |
| `health_check` | Infrastructure checks from the registered `HealthCheckRunner`. |
| `auth_status` | Authentication health from installed `AuthStatusProvider` integrations. |

When `spatie/laravel-health` is installed, the package registers its health runner by default. You can bind your own implementation:

```php
use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;

$this->app->bind(HealthCheckRunner::class, YourHealthCheckRunner::class);
```

Optional integrations register their auth providers automatically when installed, including Google Workspace, NotebookLM, and Vector Search. You can add a custom provider by implementing `AuthStatusProvider` and tagging it:

```php
$this->app->singleton(MyAuthStatusProvider::class);
$this->app->tag([MyAuthStatusProvider::class], 'mcp-ops.auth-providers');
```

## Configuration

| Setting | Environment variable | Default |
| --- | --- | --- |
| Local MCP handle | `MCP_OPS_LOCAL_HANDLE` | `ops` |
| Web MCP path | `MCP_OPS_WEB_PATH` | `/mcp/ops` |
| Auto-register Spatie health | `MCP_OPS_HEALTH_AUTO_REGISTER_SPATIE` | `true` |
| Google auth remediation | `MCP_OPS_GOOGLE_REMEDIATION` | `php artisan google:auth` |
| NotebookLM remediation | `MCP_OPS_NOTEBOOKLM_REMEDIATION` | `nlm login` |

## Tests

```bash
composer install
composer test
composer analyse
composer format -- --test
```

## License

MIT. See [LICENSE.md](LICENSE.md).
