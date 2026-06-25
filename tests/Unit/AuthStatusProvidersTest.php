<?php

use Eloquage\LaravelMcpOps\AuthStatus\GoogleAuthStatusProvider;
use Eloquage\LaravelMcpOps\AuthStatus\NotebookLmAuthStatusProvider;

it('reports google fail when no accounts exist', function () {
    $google = Mockery::mock();
    $google->shouldReceive('authStatus')->once()->andReturn([]);

    $provider = new GoogleAuthStatusProvider($google);
    $integration = $provider->integration();

    expect($integration['status'])->toBe('fail')
        ->and($integration['details'])->toContain('No Google accounts');
});

it('reports google ok when at least one account is healthy', function () {
    $google = Mockery::mock();
    $google->shouldReceive('authStatus')->once()->andReturn([
        [
            'email' => 'user@example.com',
            'healthy' => true,
        ],
    ]);

    $provider = new GoogleAuthStatusProvider($google);

    expect($provider->integration()['status'])->toBe('ok');
});

it('reports google warn when accounts exist but none are healthy', function () {
    $google = Mockery::mock();
    $google->shouldReceive('authStatus')->once()->andReturn([
        [
            'email' => 'user@example.com',
            'healthy' => false,
        ],
    ]);

    $provider = new GoogleAuthStatusProvider($google);

    expect($provider->integration()['status'])->toBe('warn');
});

it('reports notebooklm fail when doctor fails', function () {
    $notebookLm = Mockery::mock();
    $notebookLm->shouldReceive('serverInfo')->once()->andReturn([
        'success' => false,
        'error' => 'nlm not authenticated',
    ]);

    $provider = new NotebookLmAuthStatusProvider($notebookLm);
    $integration = $provider->integration();

    expect($integration['status'])->toBe('fail')
        ->and($integration['details'])->toContain('nlm not authenticated');
});

it('reports notebooklm ok when doctor succeeds', function () {
    $notebookLm = Mockery::mock();
    $notebookLm->shouldReceive('serverInfo')->once()->andReturn([
        'success' => true,
        'response' => ['status' => 'ok'],
    ]);

    $provider = new NotebookLmAuthStatusProvider($notebookLm);

    expect($provider->integration()['status'])->toBe('ok');
});
