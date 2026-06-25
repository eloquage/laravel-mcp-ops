<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Mcp\Tools;

use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;
use Eloquage\LaravelMcpOps\Mcp\Concerns\HandlesJsonToolResponse;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('health_check')]
class HealthCheckTool extends Tool
{
    use HandlesJsonToolResponse;

    protected string $description = 'Check infrastructure health: scheduler, queue, Redis, PostgreSQL, Meilisearch, embedding API.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request, Container $container): Response
    {
        if (! $container->bound(HealthCheckRunner::class)) {
            return $this->jsonResponse(fn (): array => [
                'success' => false,
                'error' => 'No HealthCheckRunner bound. Implement Eloquage\\LaravelMcpOps\\Contracts\\HealthCheckRunner in your app.',
            ]);
        }

        /** @var HealthCheckRunner $healthCheck */
        $healthCheck = $container->make(HealthCheckRunner::class);

        return $this->jsonResponse(fn (): array => [
            'success' => true,
            'checks' => $healthCheck->run(),
        ]);
    }
}
