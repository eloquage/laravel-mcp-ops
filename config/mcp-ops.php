<?php

declare(strict_types=1);

return [
    'mcp' => [
        'local_handle' => env('MCP_OPS_LOCAL_HANDLE', 'ops'),
        'web_path' => env('MCP_OPS_WEB_PATH', '/mcp/ops'),
        'middleware' => ['web'],
    ],
    'remediation' => [
        'google' => env('MCP_OPS_GOOGLE_REMEDIATION', 'php artisan google:auth'),
        'notebooklm' => env('MCP_OPS_NOTEBOOKLM_REMEDIATION', 'nlm login'),
    ],
];
