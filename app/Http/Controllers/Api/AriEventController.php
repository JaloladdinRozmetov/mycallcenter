<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutboundCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AriEventController extends Controller
{
    public function __invoke(Request $request)
    {
        $eventType = $request->input('type');
        $callId = $request->input('channel.variables.CALL_ID') ?? $request->input('channel.id');

        if (! $callId) {
            return response()->json(['message' => 'Missing call identifier'], 400);
        }

        $call = OutboundCall::find($callId);

        if (! $call) {
            return response()->json(['message' => 'Call not tracked'], 404);
        }

        match ($eventType) {
            'StasisStart' => $this->markAnswered($call),
            'StasisEnd' => $this->markCompleted($call),
            'ChannelHangupRequest' => $this->markNoAnswer($call),
            default => null,
        };

        Log::channel('loki')->info('ARI event received', [
            'event' => $eventType,
            'call_id' => $call->id,
            'status' => $call->status,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function markAnswered(OutboundCall $call): void
    {
        $call->update([
            'status' => OutboundCall::STATUS_ANSWERED,
            'answered_at' => now(),
        ]);
    }

    private function markCompleted(OutboundCall $call): void
    {
        $call->update([
            'status' => OutboundCall::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    private function markNoAnswer(OutboundCall $call): void
    {
        $call->update([
            'status' => OutboundCall::STATUS_NO_ANSWER,
        ]);
    }
}
