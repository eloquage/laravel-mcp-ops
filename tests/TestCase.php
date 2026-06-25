<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Tests;

use Eloquage\LaravelGoogleWorkspace\LaravelGoogleWorkspaceServiceProvider;
use Eloquage\LaravelMcpOps\LaravelMcpOpsServiceProvider;
use Eloquage\LaravelNotebookLm\LaravelNotebookLmServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Laravel\Mcp\Server\McpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            McpServiceProvider::class,
            EncryptionServiceProvider::class,
            LaravelGoogleWorkspaceServiceProvider::class,
            LaravelNotebookLmServiceProvider::class,
            LaravelMcpOpsServiceProvider::class,
        ];
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
    }
}
