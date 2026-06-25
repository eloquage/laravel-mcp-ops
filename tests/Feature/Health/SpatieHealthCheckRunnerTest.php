<?php

use Eloquage\LaravelMcpOps\Health\SpatieHealthCheckRunner;
use Eloquage\LaravelMcpOps\Tests\Support\OkHealthCheck;
use Spatie\Health\Facades\Health;

beforeEach(function () {
    Health::clearChecks();
    Health::checks([
        OkHealthCheck::new()->name('redis')->label('Redis'),
    ]);
});

it('maps registered spatie checks to the ops health payload', function () {
    $results = app(SpatieHealthCheckRunner::class)->run();

    expect($results)->toHaveCount(1)
        ->and($results[0]['service'])->toBe('Redis')
        ->and($results[0]['status'])->toBe('ok')
        ->and($results[0]['dependency'])->toBe('redis')
        ->and($results[0]['details'])->toBe('connected');
});
