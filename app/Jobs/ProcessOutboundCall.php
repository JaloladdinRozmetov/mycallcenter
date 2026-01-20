<?php

namespace App\Jobs;

use App\Models\CallBatch;
use App\Models\OutboundCall;
use App\Services\AsteriskAriClient;
use App\Services\TtsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOutboundCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public OutboundCall $outboundCall)
    {
    }

    public function handle(AsteriskAriClient $ariClient, TtsService $ttsService): void
    {
        $call = DB::transaction(function () {
            $call = OutboundCall::query()
                ->whereKey($this->outboundCall->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $call->increment('attempts');
            $call->update([
                'status' => OutboundCall::STATUS_DIALING,
            ]);

            return $call;
        });

        try {
            $ttsPath = $call->tts_audio_path ?? $ttsService->synthesizeForCall($call);
            $call->update([
                'tts_audio_path' => $ttsPath,
            ]);

            $response = $ariClient->originateAndPlay(
                phoneNumber: $call->phone_number,
                playbackPath: $ttsPath,
                callId: (string) $call->id
            );

            $call->update([
                'status' => OutboundCall::STATUS_COMPLETED,
                'last_response' => $response,
                'completed_at' => now(),
            ]);

            $call->batch()->increment('successful_calls');
            $this->updateBatchStatus($call);
        } catch (\Throwable $exception) {
            Log::channel('loki')->error('Outbound call failed', [
                'call_id' => $call->id,
                'phone_number' => $call->phone_number,
                'message' => $exception->getMessage(),
            ]);

            $call->update([
                'status' => OutboundCall::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
            ]);

            $call->batch()->increment('failed_calls');
            $this->updateBatchStatus($call);
            throw $exception;
        }
    }

    private function updateBatchStatus(OutboundCall $call): void
    {
        $batch = $call->batch()->withCount([
            'outboundCalls as pending_count' => fn ($query) => $query->whereNotIn('status', [
                OutboundCall::STATUS_COMPLETED,
                OutboundCall::STATUS_FAILED,
            ]),
        ])->first();

        if ($batch && $batch->pending_count === 0) {
            $batch->update([
                'status' => CallBatch::STATUS_COMPLETED,
            ]);
        } elseif ($batch && $batch->pending_count > 0 && $batch->status === CallBatch::STATUS_PENDING) {
            $batch->update([
                'status' => CallBatch::STATUS_PROCESSING,
            ]);
        }
    }
}
