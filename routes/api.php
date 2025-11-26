<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
    Route::get('/activities/{activity}/export.gpx', [ActivityController::class, 'export']);

    Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index']);

    Route::get('/friends', [\App\Http\Controllers\FriendController::class, 'friends']);
    Route::get('/friends/requests', [\App\Http\Controllers\FriendController::class, 'requests']);
    Route::post('/friends/invite', [\App\Http\Controllers\FriendController::class, 'invite']);
    Route::post('/friends/{friendRequest}/accept', [\App\Http\Controllers\FriendController::class, 'accept']);
    Route::post('/friends/{friendRequest}/reject', [\App\Http\Controllers\FriendController::class, 'reject']);

    Route::post('/activities/{activity}/kudos', [\App\Http\Controllers\KudosController::class, 'store']);
    Route::delete('/activities/{activity}/kudos', [\App\Http\Controllers\KudosController::class, 'destroy']);

    Route::get('/activities/{activity}/comments', [\App\Http\Controllers\CommentController::class, 'index']);
    Route::post('/activities/{activity}/comments', [\App\Http\Controllers\CommentController::class, 'store']);


    Route::get('/me', function (Request $request) {
        return new UserResource($request->user());
    });
});

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function (): void {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);

        Route::get('/activities', [AdminActivityController::class, 'index']);
        Route::delete('/activities/{activity}', [AdminActivityController::class, 'destroy']);

        Route::get('/stats', StatsController::class);
    });
