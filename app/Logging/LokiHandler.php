<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Logger;
use Illuminate\Support\Facades\Http;

class LokiHandler extends AbstractProcessingHandler
{
    public function __construct()
    {
        parent::__construct(Logger::DEBUG, true);
    }

    protected function write(LogRecord $record): void
    {
        $endpoint = config('logging.loki.endpoint');

        if (! $endpoint) {
            return;
        }

        $labels = array_merge([
            'service' => config('app.name', 'laravel'),
            'env' => config('app.env'),
        ], config('logging.loki.labels', []));

        $payload = [
            'streams' => [
                [
                    'stream' => $labels,
                    'values' => [
                        [sprintf('%d000000', now()->timestamp), json_encode($record->toArray())],
                    ],
                ],
            ],
        ];

        $request = Http::withHeaders($this->headers());

        if ($tenant = config('logging.loki.tenant_id')) {
            $request = $request->withHeaders(['X-Scope-OrgID' => $tenant]);
        }

        $request->post(rtrim($endpoint, '/').'/loki/api/v1/push', $payload);
    }

    private function headers(): array
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($basicAuth = config('logging.loki.basic_auth')) {
            $headers['Authorization'] = 'Basic '.$basicAuth;
        }

        return $headers;
    }
}
