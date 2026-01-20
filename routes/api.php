<?php

use App\Http\Controllers\Api\OutboundCallController;
use Illuminate\Support\Facades\Route;

Route::prefix('outbound')->group(function () {
    Route::post('/calls', [OutboundCallController::class, 'store']);
    Route::post('/calls/upload', [OutboundCallController::class, 'upload']);
});

Route::post('/ari/events', \App\Http\Controllers\Api\AriEventController::class);
