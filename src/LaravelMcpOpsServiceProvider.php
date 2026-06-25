<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps;

use Eloquage\LaravelGoogleWorkspace\Services\Google\GoogleOAuthService;
use Eloquage\LaravelMcpOps\AuthStatus\GoogleAuthStatusProvider;
use Eloquage\LaravelMcpOps\AuthStatus\NotebookLmAuthStatusProvider;
use Eloquage\LaravelMcpOps\AuthStatus\VectorSearchAuthStatusProvider;
use Eloquage\LaravelMcpOps\Contracts\HealthCheckRunner;
use Eloquage\LaravelMcpOps\Health\SpatieHealthCheckRunner;
use Eloquage\LaravelMcpOps\Mcp\Servers\OpsServer;
use Eloquage\LaravelMcpOps\Services\McpAuthStatusService;
use Eloquage\LaravelNotebookLm\NotebookLmService;
use Eloquage\LaravelVectorSearch\Managers\EmbeddingProviderManager;
use Illuminate\Contracts\Container\Container;
use Laravel\Mcp\Facades\Mcp;
use Spatie\Health\Health;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMcpOpsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mcp-ops')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->registerAuthStatusProviders();
        $this->registerHealthCheckRunner();

        $this->app->singleton(McpAuthStatusService::class, function (Container $app): McpAuthStatusService {
            return new McpAuthStatusService($app->tagged('mcp-ops.auth-providers'));
        });
    }

    public function packageBooted(): void
    {
        if (! class_exists(Mcp::class)) {
            return;
        }

        Mcp::local(
            (string) config('mcp-ops.mcp.local_handle', 'ops'),
            OpsServer::class,
        );

        Mcp::web(
            (string) config('mcp-ops.mcp.web_path', '/mcp/ops'),
            OpsServer::class,
        )->middleware((array) config('mcp-ops.mcp.middleware', ['web']));
    }

    private function registerAuthStatusProviders(): void
    {
        if (class_exists(GoogleOAuthService::class)) {
            $this->app->singleton(GoogleAuthStatusProvider::class, function (Container $app): GoogleAuthStatusProvider {
                return new GoogleAuthStatusProvider(
                    $app->make(GoogleOAuthService::class),
                );
            });

            $this->app->tag([GoogleAuthStatusProvider::class], 'mcp-ops.auth-providers');
        }

        if (class_exists(NotebookLmService::class)) {
            $this->app->singleton(NotebookLmAuthStatusProvider::class, function (Container $app): NotebookLmAuthStatusProvider {
                return new NotebookLmAuthStatusProvider(
                    $app->make(NotebookLmService::class),
                );
            });

            $this->app->tag([NotebookLmAuthStatusProvider::class], 'mcp-ops.auth-providers');
        }

        if (class_exists(EmbeddingProviderManager::class)) {
            $this->app->singleton(VectorSearchAuthStatusProvider::class, function (Container $app): VectorSearchAuthStatusProvider {
                return new VectorSearchAuthStatusProvider(
                    $app->make(EmbeddingProviderManager::class),
                );
            });

            $this->app->tag([VectorSearchAuthStatusProvider::class], 'mcp-ops.auth-providers');
        }
    }

    private function registerHealthCheckRunner(): void
    {
        if (! config('mcp-ops.health.auto_register_spatie', true)) {
            return;
        }

        if (! class_exists(Health::class)) {
            return;
        }

        if ($this->app->bound(HealthCheckRunner::class)) {
            return;
        }

        $this->app->singleton(HealthCheckRunner::class, SpatieHealthCheckRunner::class);
    }
}
