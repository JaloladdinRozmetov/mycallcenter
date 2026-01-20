<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallBatch extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'source',
        'name',
        'text_to_speak',
        'status',
        'total_targets',
        'successful_calls',
        'failed_calls',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function outboundCalls(): HasMany
    {
        return $this->hasMany(OutboundCall::class);
    }
}
