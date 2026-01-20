<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outbound_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_batch_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number')->index();
            $table->string('status')->default('queued')->index();
            $table->string('ari_channel_id')->nullable()->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('tts_text')->nullable();
            $table->string('tts_audio_path')->nullable();
            $table->json('last_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_calls');
    }
};
