<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Health;

use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Health as HealthRegistry;
use Throwable;

final class SpatieHealthCheckRunner implements HealthCheckRunner
{
    public function __construct(
        private HealthRegistry $health,
    ) {}

    /**
     * @return list<array{service: string, status: string, process: string, dependency: string, details: string}>
     */
    public function run(): array
    {
        return $this->health->registeredChecks()
            ->map(function (Check $check): array {
                $result = $this->runCheck($check);

                return [
                    'service' => $check->getLabel(),
                    'status' => (string) $result->status,
                    'process' => 'n/a',
                    'dependency' => $check->getName(),
                    'details' => $result->getShortSummary(),
                ];
            })
            ->values()
            ->all();
    }

    private function runCheck(Check $check): Result
    {
        if (! $check->shouldRun()) {
            return (new Result(Status::skipped()))
                ->check($check)
                ->endedAt(now());
        }

        try {
            return $check->run()
                ->check($check)
                ->endedAt(now());
        } catch (Throwable $exception) {
            report($exception);

            return $check->markAsCrashed()
                ->check($check)
                ->endedAt(now());
        }
    }
}
