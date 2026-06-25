<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\AuthStatus;

use Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider;

class GoogleAuthStatusProvider implements AuthStatusProvider
{
    public function __construct(
        private mixed $googleOAuth,
    ) {}

    public function integration(): array
    {
        $accounts = $this->googleOAuth->authStatus();
        $remediation = (string) config('mcp-ops.remediation.google', 'php artisan google:auth');

        if ($accounts === []) {
            return [
                'service' => 'Google',
                'status' => 'fail',
                'details' => 'No Google accounts authenticated.',
                'remediation' => $remediation,
            ];
        }

        $healthyCount = count(array_filter(
            $accounts,
            fn (array $account): bool => ($account['healthy'] ?? false) === true,
        ));

        if ($healthyCount > 0) {
            return [
                'service' => 'Google',
                'status' => 'ok',
                'details' => "{$healthyCount} of ".count($accounts).' account(s) healthy.',
                'remediation' => $remediation,
            ];
        }

        return [
            'service' => 'Google',
            'status' => 'warn',
            'details' => count($accounts).' account(s) stored but none are fully healthy (check refresh token and scopes).',
            'remediation' => $remediation,
        ];
    }

    public function payload(): array
    {
        return [
            'google' => [
                'default_account' => config('google.default_account'),
                'required_scopes' => config('google.scopes', []),
                'accounts' => $this->googleOAuth->authStatus(),
            ],
        ];
    }
}
