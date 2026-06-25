<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\AuthStatus;

use Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider;

class NotebookLmAuthStatusProvider implements AuthStatusProvider
{
    public function __construct(
        private mixed $notebookLm,
    ) {}

    public function integration(): array
    {
        $notebookLm = $this->notebookLmStatus();
        $remediation = (string) config('mcp-ops.remediation.notebooklm', 'nlm login');

        if (! $notebookLm['success']) {
            return [
                'service' => 'NotebookLM',
                'status' => 'fail',
                'details' => (string) ($notebookLm['error'] ?? 'NotebookLM doctor check failed.'),
                'remediation' => $remediation,
            ];
        }

        return [
            'service' => 'NotebookLM',
            'status' => 'ok',
            'details' => 'NotebookLM CLI auth is healthy.',
            'remediation' => $remediation,
        ];
    }

    public function payload(): array
    {
        return [
            'notebooklm' => $this->notebookLmStatus(),
        ];
    }

    /**
     * @return array{success: bool, response?: mixed, error?: string}
     */
    private function notebookLmStatus(): array
    {
        return $this->notebookLm->serverInfo();
    }
}
