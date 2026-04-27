<?php

use App\Http\Controllers\OrionChatController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::post('orion/chat', [OrionChatController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('orion.chat.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
