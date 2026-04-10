<?php

use Illuminate\Support\Facades\Route;
use ITHilbert\LaravelKit\Controllers\AiDashboardController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('devtools/ai')->group(function () {
        Route::get('/', [AiDashboardController::class, 'index'])->name('ai.dashboard');
        Route::post('/pause', [AiDashboardController::class, 'togglePause'])->name('ai.pause');
        Route::get('/{id}', [AiDashboardController::class, 'show'])->name('ai.show');
        Route::post('/{id}/respond', [AiDashboardController::class, 'respond'])->name('ai.respond');
        Route::delete('/{id}', [AiDashboardController::class, 'destroy'])->name('ai.destroy');
        Route::post('/', [AiDashboardController::class, 'store'])->name('ai.store');
    });
});
