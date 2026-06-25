<?php

use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;
use Eloquage\LaravelMcpOps\Mcp\Tools\HealthCheckTool;
use Illuminate\Contracts\Container\Container;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

it('returns health check results as json when runner is bound', function () {
    $runner = Mockery::mock(HealthCheckRunner::class);
    $runner->shouldReceive('run')->once()->andReturn([
        [
            'service' => 'Redis',
            'status' => 'ok',
            'process' => 'n/a',
            'dependency' => 'connected',
            'details' => '127.0.0.1:6379',
        ],
    ]);

    app()->instance(HealthCheckRunner::class, $runner);

    $response = app(HealthCheckTool::class)->handle(
        new Request([]),
        app(Container::class),
    );

    expect($response)->toBeInstanceOf(Response::class);

    $payload = json_decode((string) $response->content(), true);

    expect($payload['success'])->toBeTrue()
        ->and($payload['checks'])->toHaveCount(1)
        ->and($payload['checks'][0]['service'])->toBe('Redis');
});

it('returns error when health check runner is not bound', function () {
    app()->offsetUnset(HealthCheckRunner::class);

    $response = app(HealthCheckTool::class)->handle(
        new Request([]),
        app(Container::class),
    );

    $payload = json_decode((string) $response->content(), true);

    expect($payload['success'])->toBeFalse()
        ->and($payload['error'])->toContain('HealthCheckRunner');
});
