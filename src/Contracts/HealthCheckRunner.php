<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Contracts;

interface HealthCheckRunner
{
    /**
     * @return list<array{service: string, status: string, process: string, dependency: string, details: string}>
     */
    public function run(): array;
}
