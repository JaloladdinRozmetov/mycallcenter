<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessOutboundCall;
use App\Models\CallBatch;
use App\Models\OutboundCall;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class OutboundCallController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:2000'],
            'phone_numbers' => ['required', 'array', 'min:1'],
            'phone_numbers.*' => ['required', 'string', 'max:32'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $batch = DB::transaction(function () use ($validated) {
            $batch = CallBatch::create([
                'source' => 'api',
                'name' => $validated['name'] ?? null,
                'text_to_speak' => $validated['text'],
                'status' => CallBatch::STATUS_PROCESSING,
                'total_targets' => count($validated['phone_numbers']),
            ]);

            $calls = collect($validated['phone_numbers'])
                ->unique()
                ->map(fn ($number) => [
                    'call_batch_id' => $batch->id,
                    'phone_number' => $number,
                    'status' => OutboundCall::STATUS_QUEUED,
                    'tts_text' => $validated['text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            OutboundCall::insert($calls->all());

            return $batch;
        });

        $batch->outboundCalls()
            ->latest('id')
            ->each(fn (OutboundCall $call) => ProcessOutboundCall::dispatch($call));

        Log::channel('loki')->info('Outbound batch created', [
            'batch_id' => $batch->id,
            'total_targets' => $batch->total_targets,
            'source' => $batch->source,
        ]);

        return response()->json([
            'batch_id' => $batch->id,
            'status' => $batch->status,
            'total' => $batch->total_targets,
        ], 201);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:2000'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $phoneNumbers = $this->extractPhoneNumbers($validated['file']);

        if ($phoneNumbers->isEmpty()) {
            return response()->json(['message' => 'No phone numbers detected in the file.'], 422);
        }

        $batch = DB::transaction(function () use ($validated, $phoneNumbers) {
            $storedPath = $validated['file']->storePublicly('call-batches');

            $batch = CallBatch::create([
                'source' => 'upload',
                'name' => $validated['name'] ?? null,
                'text_to_speak' => $validated['text'],
                'status' => CallBatch::STATUS_PROCESSING,
                'total_targets' => $phoneNumbers->count(),
                'meta' => [
                    'file' => $storedPath,
                    'original_name' => $validated['file']->getClientOriginalName(),
                ],
            ]);

            $calls = $phoneNumbers->unique()
                ->map(fn ($number) => [
                    'call_batch_id' => $batch->id,
                    'phone_number' => $number,
                    'status' => OutboundCall::STATUS_QUEUED,
                    'tts_text' => $validated['text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            OutboundCall::insert($calls->all());

            return $batch;
        });

        $batch->outboundCalls()
            ->latest('id')
            ->each(fn (OutboundCall $call) => ProcessOutboundCall::dispatch($call));

        Log::channel('loki')->info('Outbound batch uploaded', [
            'batch_id' => $batch->id,
            'total_targets' => $batch->total_targets,
            'source' => $batch->source,
        ]);

        return response()->json([
            'batch_id' => $batch->id,
            'status' => $batch->status,
            'total' => $batch->total_targets,
        ], 201);
    }

    private function extractPhoneNumbers(UploadedFile $file): Collection
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv') {
            $content = $file->getContent();
            $lines = preg_split('/\r\n|\r|\n/', $content);

            return collect($lines)
                ->map(fn ($line) => trim(Str::of($line)->replace('"', '')))
                ->filter();
        }

        $collections = Excel::toCollection(null, $file);

        return $collections
            ->flatten(1)
            ->map(fn ($row) => trim((string) ($row[0] ?? '')))
            ->filter();
    }
}
