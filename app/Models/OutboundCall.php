<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboundCall extends Model
{
    use HasFactory;

    public const STATUS_QUEUED = 'queued';
    public const STATUS_DIALING = 'dialing';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_PLAYBACK = 'playback';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_NO_ANSWER = 'no_answer';

    protected $fillable = [
        'call_batch_id',
        'phone_number',
        'status',
        'ari_channel_id',
        'attempts',
        'answered_at',
        'completed_at',
        'tts_text',
        'tts_audio_path',
        'last_response',
        'error_message',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_response' => 'array',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CallBatch::class, 'call_batch_id');
    }
}
