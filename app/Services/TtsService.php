<?php

namespace App\Services;

use App\Models\OutboundCall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TtsService
{
    /**
     * Turn the text for a call into an audio file and return a path accessible to Asterisk.
     */
    public function synthesizeForCall(OutboundCall $call): string
    {
        $text = $call->tts_text ?: $call->batch?->text_to_speak;

        if (! $text) {
            throw new \RuntimeException('No TTS text provided for call.');
        }

        $response = Http::withHeaders($this->headers())
            ->timeout((int) config('services.tts.timeout', 15))
            ->post(rtrim(config('services.tts.endpoint'), '/').'/synthesize', [
                'text' => $text,
                'voice' => config('services.tts.voice', 'default'),
                'language' => config('services.tts.language', 'en-US'),
                'format' => config('services.tts.format', 'wav'),
            ])
            ->throw();

        $audioBinary = $response->body();
        $path = 'tts/'.Str::uuid().'.'.config('services.tts.format', 'wav');
        Storage::disk('public')->put($path, $audioBinary);

        return Storage::disk('public')->path($path);
    }

    private function headers(): array
    {
        $headers = [];

        if ($token = config('services.tts.api_key')) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
