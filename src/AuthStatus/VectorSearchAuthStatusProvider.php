<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\AuthStatus;

use Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider;

class VectorSearchAuthStatusProvider implements AuthStatusProvider
{
    public function __construct(
        private object $embeddingProviders,
    ) {}

    public function integration(): array
    {
        $provider = (string) config('vector-search.default_embedding_provider', 'openai');
        $bridge = (string) config('vector-search-ai.embedding_bridge', 'off');
        $configured = $this->embeddingProviders->isConfigured($provider);
        $remediation = 'Set OPENAI_API_KEY or enable VECTOR_SEARCH_AI_EMBEDDING_BRIDGE=laravel-ai with valid Laravel AI credentials.';

        if ($configured) {
            return [
                'service' => 'Vector Search',
                'status' => 'ok',
                'details' => "Embedding provider [{$provider}] is configured. Bridge: {$bridge}.",
                'remediation' => $remediation,
            ];
        }

        return [
            'service' => 'Vector Search',
            'status' => 'fail',
            'details' => "Embedding provider [{$provider}] is not configured. Bridge: {$bridge}.",
            'remediation' => $remediation,
        ];
    }

    public function payload(): array
    {
        return [
            'vector_search' => [
                'default_index' => config('vector-search-ai.default_index'),
                'embedding_bridge' => config('vector-search-ai.embedding_bridge'),
                'embedding_provider' => config('vector-search.default_embedding_provider'),
                'mcp_local_handle' => config('vector-search-ai.mcp.local_handle'),
            ],
        ];
    }
}
