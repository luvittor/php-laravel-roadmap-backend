<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/ping-auth', [AuthController::class, 'pingAuth']);

        Route::get('/columns/{year}/{month}/cards', [\App\Http\Controllers\ColumnController::class, 'cards']);
        Route::post('/cards', [\App\Http\Controllers\CardController::class, 'store']);
        Route::get('/cards/{card}', [\App\Http\Controllers\CardController::class, 'show'])
            ->middleware('can:view,card');
        Route::patch('/cards/{card}/title', [\App\Http\Controllers\CardController::class, 'updateTitle'])
            ->middleware('can:update,card');
        Route::patch('/cards/{card}/status', [\App\Http\Controllers\CardController::class, 'updateStatus'])
            ->middleware('can:update,card');
        Route::patch('/cards/{card}/position', [\App\Http\Controllers\CardController::class, 'updatePosition'])
            ->middleware('can:update,card');
        Route::delete('/cards/{card}', [\App\Http\Controllers\CardController::class, 'destroy'])
            ->middleware('can:delete,card');
    });
});

