<?php

namespace App\Services;

use App\Models\OutboundCall;

class OutboundCallAriService
{
    public function __construct(
        private readonly AsteriskAriClient $ariClient,
        private readonly TtsService $ttsService,
    ) {
    }

    public function originate(OutboundCall $call): array
    {
        $ttsPath = $this->ensureTtsAudio($call);

        $response = $this->ariClient->originateChannel(
            endpoint: config('services.ari.outbound_endpoint').'/'.$call->phone_number,
            extension: $call->phone_number,
            app: config('services.ari.app'),
            variables: ['CALL_ID' => (string) $call->id],
            callerId: config('services.ari.caller_id', config('app.name', 'CallCenter')),
            timeout: (int) config('services.ari.dial_timeout', 20),
            appArgs: $ttsPath
        );

        $call->fill([
            'ari_channel_id' => $call->ari_channel_id ?: ($response['id'] ?? null),
            'tts_audio_path' => $ttsPath,
            'last_response' => $response,
        ])->save();

        return $response;
    }

    public function requestPlayback(OutboundCall $call): array
    {
        $channelId = $this->assertChannel($call);
        $media = $call->tts_audio_path ?? $this->ensureTtsAudio($call);

        return $this->ariClient->playOnChannel($channelId, 'sound:'.$media);
    }

    public function hangup(OutboundCall $call): void
    {
        $channelId = $this->assertChannel($call);

        $this->ariClient->hangupChannel($channelId);
    }

    private function ensureTtsAudio(OutboundCall $call): string
    {
        if ($call->tts_audio_path) {
            return $call->tts_audio_path;
        }

        $path = $this->ttsService->synthesizeForCall($call);
        $call->forceFill(['tts_audio_path' => $path])->save();

        return $path;
    }

    private function assertChannel(OutboundCall $call): string
    {
        if (! $call->ari_channel_id) {
            throw new \RuntimeException('ARI channel is not known for this call.');
        }

        return $call->ari_channel_id;
    }
}
