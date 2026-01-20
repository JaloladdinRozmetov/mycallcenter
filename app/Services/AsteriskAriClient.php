<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AsteriskAriClient
{
    public function originateAndPlay(string $phoneNumber, string $playbackPath, ?string $callId = null): array
    {
        $endpoint = rtrim(config('services.ari.base_uri'), '/');
        $response = Http::withBasicAuth(
            config('services.ari.username'),
            config('services.ari.password')
        )->post($endpoint.'/channels', [
            'endpoint' => config('services.ari.outbound_endpoint').'/'.$phoneNumber,
            'extension' => $phoneNumber,
            'app' => config('services.ari.app'),
            'appArgs' => $playbackPath,
            'callerId' => config('services.ari.caller_id', config('app.name', 'CallCenter')),
            'timeout' => config('services.ari.dial_timeout', 20),
            'variables' => [
                'CALL_ID' => $callId,
            ],
        ])->throw();

        return $response->json() ?? [];
    }

    public function markPlayback(string $channelId, string $playbackPath): array
    {
        $endpoint = rtrim(config('services.ari.base_uri'), '/');
        $response = Http::withBasicAuth(
            config('services.ari.username'),
            config('services.ari.password')
        )->post($endpoint."/channels/{$channelId}/play", [
            'media' => 'sound:'.$playbackPath,
        ])->throw();

        return $response->json() ?? [];
    }
}
