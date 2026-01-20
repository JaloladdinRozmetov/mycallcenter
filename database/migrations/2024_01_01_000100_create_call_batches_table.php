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
        Schema::create('call_batches', function (Blueprint $table) {
            $table->id();
            $table->string('source')->index();
            $table->string('name')->nullable();
            $table->text('text_to_speak');
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('total_targets')->default(0);
            $table->unsignedInteger('successful_calls')->default(0);
            $table->unsignedInteger('failed_calls')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_batches');
    }
};
