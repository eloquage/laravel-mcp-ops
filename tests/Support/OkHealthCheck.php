<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Tests\Support;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

final class OkHealthCheck extends Check
{
    public function run(): Result
    {
        return Result::make()
            ->ok('Redis is reachable.')
            ->shortSummary('connected');
    }
}
