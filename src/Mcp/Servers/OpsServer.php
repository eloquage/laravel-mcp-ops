<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Mcp\Servers;

use Eloquage\LaravelMcpOps\Mcp\Tools\AuthStatusTool;
use Eloquage\LaravelMcpOps\Mcp\Tools\HealthCheckTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Tool;

#[Name('ops')]
#[Version('1.0.0')]
#[Instructions(<<<'INSTRUCTIONS'
Use this server to verify infrastructure before long agent sessions.

Call `health_check` and `auth_status` before Gmail, Calendar, Drive, or NotebookLM work.

`health_check` reports infrastructure status when a HealthCheckRunner is bound in the host app.
`auth_status` aggregates OAuth and CLI auth health across installed integrations.
INSTRUCTIONS)]
class OpsServer extends Server
{
    /**
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        HealthCheckTool::class,
        AuthStatusTool::class,
    ];
}
