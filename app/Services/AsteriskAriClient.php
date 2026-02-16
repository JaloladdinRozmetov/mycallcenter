<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AsteriskAriService
{
    /**
     * Send a request to Asterisk ARI.
     * @param string $method   HTTP method (POST, DELETE)
     * @param string $endpoint ARI endpoint path (e.g. /channels)
     * @param array  $payload  Request body parameter
     * @return array
     * @throws RequestException
     */
    public function send(string $method, string $endpoint, array $payload = []): array {
        $baseUri = rtrim((string) config('services.ari.base_uri'), '/');
        $url     = $baseUri . $endpoint;
        $payload = array_filter($payload, static function ($value) {
            if ($value === 0 || $value === '0') {return true;}
            return $value !== null && $value !== [];
        });
        try {
            $client = Http::withBasicAuth(
                (string) config('services.ari.username'),
                (string) config('services.ari.password')
            );
            $response = match (strtolower($method)) {
                'post'   => $client->post($url, $payload),
                'delete' => $client->delete($url),
                'get'    => $client->get($url, $payload),
                default  => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };
            $response->throw();
            return $response->json() ?? [];
        } catch (RequestException $e) {
            Log::error('ARI request failed', [
                'status' => optional($e->response)->status(),
                'error'  => $e->getMessage(),
            ]);throw $e;
        }
    }
}
