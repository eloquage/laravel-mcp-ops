<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Mcp\Tools;

use Eloquage\LaravelMcpOps\Mcp\Concerns\HandlesJsonToolResponse;
use Eloquage\LaravelMcpOps\Services\McpAuthStatusService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('auth_status')]
class AuthStatusTool extends Tool
{
    use HandlesJsonToolResponse;

    protected string $description = 'Aggregate auth health across installed integrations. Follow remediation hints if unhealthy.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request, McpAuthStatusService $authStatus): Response
    {
        return $this->jsonResponse(fn (): array => $authStatus->status());
    }
}
