<?php

use App\Http\Controllers\OrionChatController;
use App\Http\Controllers\VeoFinalVideoController;
use App\Http\Controllers\VeoNonceController;
use App\Http\Controllers\VeoVideoGeneratorController;
use App\Http\Controllers\VeoVideoUrlGeneratorController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::post('orion/chat', [OrionChatController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('orion.chat.store');

Route::get('orion/chat/status', [OrionChatController::class, 'status'])
    ->middleware('throttle:60,1')
    ->name('orion.chat.status');

Route::get('veo/nonce', VeoNonceController::class)
    ->middleware('throttle:60,1')
    ->name('veo.nonce');

Route::post('veo/generate', VeoVideoGeneratorController::class)
    ->middleware('throttle:20,1')
    ->name('veo.generate');

Route::post('veo/generate-video-url', VeoVideoUrlGeneratorController::class)
    ->middleware('throttle:20,1')
    ->name('veo.generate-video-url');

Route::post('veo/video-url', VeoFinalVideoController::class)
    ->middleware('throttle:60,1')
    ->name('veo.video-url');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
