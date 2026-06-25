<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Contracts;

interface AuthStatusProvider
{
    /**
     * @return array{service: string, status: string, details: string, remediation: string}
     */
    public function integration(): array;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;
}
