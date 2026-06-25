<?php

declare(strict_types=1);

namespace Eloquage\LaravelMcpOps\Services;

use Eloquage\LaravelMcpOps\Contracts\AuthStatusProvider;

class McpAuthStatusService
{
    /**
     * @param  iterable<AuthStatusProvider>  $providers
     */
    public function __construct(
        private iterable $providers,
    ) {}

    /**
     * @return array{
     *     success: bool,
     *     overall: string,
     *     integrations: list<array{service: string, status: string, details: string, remediation: string}>,
     * }
     */
    public function status(): array
    {
        $integrations = [];
        $response = [
            'success' => true,
            'overall' => 'ok',
        ];

        foreach ($this->providers as $provider) {
            $integrations[] = $provider->integration();

            foreach ($provider->payload() as $key => $value) {
                $response[$key] = $value;
            }
        }

        $response['integrations'] = $integrations;
        $response['overall'] = $this->resolveOverall($integrations);

        return $response;
    }

    /**
     * @param  list<array{service: string, status: string, details: string, remediation: string}>  $integrations
     */
    private function resolveOverall(array $integrations): string
    {
        foreach ($integrations as $integration) {
            if ($integration['status'] === 'fail') {
                return 'fail';
            }
        }

        foreach ($integrations as $integration) {
            if ($integration['status'] === 'warn') {
                return 'warn';
            }
        }

        return 'ok';
    }
}
