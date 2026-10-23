<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Tests;

use Eloquage\LaravelMcpOps\LaravelMcpOpsServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Laravel\Mcp\Server\McpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Health\HealthServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        $providers = [
            McpServiceProvider::class,
            EncryptionServiceProvider::class,
            HealthServiceProvider::class,
            LaravelMcpOpsServiceProvider::class,
        ];

        foreach ([
            'Eloquage\\LaravelGoogleWorkspace\\LaravelGoogleWorkspaceServiceProvider',
            'Eloquage\\LaravelNotebookLm\\LaravelNotebookLmServiceProvider',
        ] as $provider) {
            if (class_exists($provider)) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

        $app['config']->set('google.scopes', [
            'https://www.googleapis.com/auth/gmail.readonly',
        ]);

        $app['config']->set('mcp-ops.remediation', [
            'google' => 'php artisan google:auth',
            'notebooklm' => 'nlm login',
        ]);

        $app['config']->set('mcp-ops.health.auto_register_spatie', false);
    }
}
