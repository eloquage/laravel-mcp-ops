<?php

use Eloquage\LaravelMcpOps\Mcp\Tools\AuthStatusTool;
use Eloquage\LaravelMcpOps\Services\McpAuthStatusService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

it('returns aggregated auth status as json', function () {
    $service = Mockery::mock(McpAuthStatusService::class);
    $service->shouldReceive('status')->once()->andReturn([
        'success' => true,
        'overall' => 'ok',
        'integrations' => [
            ['service' => 'Google', 'status' => 'ok', 'details' => '1 of 1 account(s) healthy.', 'remediation' => 'php artisan google:auth'],
            ['service' => 'NotebookLM', 'status' => 'ok', 'details' => 'NotebookLM CLI auth is healthy.', 'remediation' => 'nlm login'],
        ],
        'google' => ['default_account' => null, 'required_scopes' => [], 'accounts' => []],
        'notebooklm' => ['success' => true, 'response' => []],
    ]);

    $response = app(AuthStatusTool::class)->handle(new Request([]), $service);

    expect($response)->toBeInstanceOf(Response::class);

    $payload = json_decode((string) $response->content(), true);

    expect($payload['success'])->toBeTrue()
        ->and($payload['overall'])->toBe('ok')
        ->and($payload['integrations'])->toHaveCount(2);
});
