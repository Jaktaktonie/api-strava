<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    require __DIR__.'/auth.php';
});

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::apiResource('activities', ActivityController::class);

    Route::get('/me', function (Request $request) {
        return new UserResource($request->user());
    });
});
