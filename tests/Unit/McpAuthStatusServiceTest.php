<?php

use Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider;
use Eloquage\LaravelMcpOps\Services\McpAuthStatusService;

it('reports overall ok when google and notebooklm are healthy', function () {
    $google = Mockery::mock(AuthStatusProvider::class);
    $google->shouldReceive('integration')->once()->andReturn([
        'service' => 'Google',
        'status' => 'ok',
        'details' => '1 of 1 account(s) healthy.',
        'remediation' => 'php artisan google:auth',
    ]);
    $google->shouldReceive('payload')->once()->andReturn([
        'google' => [
            'default_account' => null,
            'required_scopes' => [],
            'accounts' => [['email' => 'user@example.com', 'healthy' => true]],
        ],
    ]);

    $notebookLm = Mockery::mock(AuthStatusProvider::class);
    $notebookLm->shouldReceive('integration')->once()->andReturn([
        'service' => 'NotebookLM',
        'status' => 'ok',
        'details' => 'NotebookLM CLI auth is healthy.',
        'remediation' => 'nlm login',
    ]);
    $notebookLm->shouldReceive('payload')->once()->andReturn([
        'notebooklm' => ['success' => true, 'response' => ['status' => 'ok']],
    ]);

    $result = (new McpAuthStatusService([$google, $notebookLm]))->status();

    expect($result['success'])->toBeTrue()
        ->and($result['overall'])->toBe('ok')
        ->and($result['integrations'])->toHaveCount(2)
        ->and($result['integrations'][0]['service'])->toBe('Google')
        ->and($result['integrations'][0]['status'])->toBe('ok')
        ->and($result['integrations'][1]['service'])->toBe('NotebookLM')
        ->and($result['integrations'][1]['status'])->toBe('ok');
});

it('reports overall fail when no google accounts exist', function () {
    $google = Mockery::mock(AuthStatusProvider::class);
    $google->shouldReceive('integration')->once()->andReturn([
        'service' => 'Google',
        'status' => 'fail',
        'details' => 'No Google accounts authenticated.',
        'remediation' => 'php artisan google:auth',
    ]);
    $google->shouldReceive('payload')->once()->andReturn([
        'google' => ['default_account' => null, 'required_scopes' => [], 'accounts' => []],
    ]);

    $notebookLm = Mockery::mock(AuthStatusProvider::class);
    $notebookLm->shouldReceive('integration')->once()->andReturn([
        'service' => 'NotebookLM',
        'status' => 'ok',
        'details' => 'NotebookLM CLI auth is healthy.',
        'remediation' => 'nlm login',
    ]);
    $notebookLm->shouldReceive('payload')->once()->andReturn([
        'notebooklm' => ['success' => true, 'response' => []],
    ]);

    $result = (new McpAuthStatusService([$google, $notebookLm]))->status();

    expect($result['overall'])->toBe('fail')
        ->and($result['integrations'][0]['status'])->toBe('fail');
});

it('reports overall warn when google accounts exist but none are healthy', function () {
    $google = Mockery::mock(AuthStatusProvider::class);
    $google->shouldReceive('integration')->once()->andReturn([
        'service' => 'Google',
        'status' => 'warn',
        'details' => '1 account(s) stored but none are fully healthy (check refresh token and scopes).',
        'remediation' => 'php artisan google:auth',
    ]);
    $google->shouldReceive('payload')->once()->andReturn([
        'google' => ['default_account' => null, 'required_scopes' => [], 'accounts' => [['healthy' => false]]],
    ]);

    $notebookLm = Mockery::mock(AuthStatusProvider::class);
    $notebookLm->shouldReceive('integration')->once()->andReturn([
        'service' => 'NotebookLM',
        'status' => 'ok',
        'details' => 'NotebookLM CLI auth is healthy.',
        'remediation' => 'nlm login',
    ]);
    $notebookLm->shouldReceive('payload')->once()->andReturn([
        'notebooklm' => ['success' => true, 'response' => []],
    ]);

    $result = (new McpAuthStatusService([$google, $notebookLm]))->status();

    expect($result['overall'])->toBe('warn')
        ->and($result['integrations'][0]['status'])->toBe('warn');
});

it('reports notebooklm integration as fail when doctor fails', function () {
    $google = Mockery::mock(AuthStatusProvider::class);
    $google->shouldReceive('integration')->once()->andReturn([
        'service' => 'Google',
        'status' => 'ok',
        'details' => '1 of 1 account(s) healthy.',
        'remediation' => 'php artisan google:auth',
    ]);
    $google->shouldReceive('payload')->once()->andReturn([
        'google' => ['default_account' => null, 'required_scopes' => [], 'accounts' => [['healthy' => true]]],
    ]);

    $notebookLm = Mockery::mock(AuthStatusProvider::class);
    $notebookLm->shouldReceive('integration')->once()->andReturn([
        'service' => 'NotebookLM',
        'status' => 'fail',
        'details' => 'nlm not authenticated',
        'remediation' => 'nlm login',
    ]);
    $notebookLm->shouldReceive('payload')->once()->andReturn([
        'notebooklm' => ['success' => false, 'error' => 'nlm not authenticated'],
    ]);

    $result = (new McpAuthStatusService([$google, $notebookLm]))->status();

    expect($result['overall'])->toBe('fail')
        ->and($result['integrations'][1]['status'])->toBe('fail')
        ->and($result['integrations'][1]['details'])->toContain('nlm not authenticated');
});

it('returns empty integrations when no providers are registered', function () {
    $result = (new McpAuthStatusService([]))->status();

    expect($result['success'])->toBeTrue()
        ->and($result['overall'])->toBe('ok')
        ->and($result['integrations'])->toBe([]);
});
