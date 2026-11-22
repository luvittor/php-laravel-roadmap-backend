<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/ping-auth', [AuthController::class, 'pingAuth']);

        Route::get('/columns/{year}/{month}/cards', [ColumnController::class, 'cards']);
        Route::post('/columns/{year}/{month}', [ColumnController::class, 'ensure']);

        // no middleware here, as the authorization is handled in the CardController
        // needs to find the column fist
        Route::post('/cards', [CardController::class, 'store']);

        Route::get('/cards/{card}', [CardController::class, 'show'])
            ->middleware('can:view,card')
            ->name('cards.show');
        Route::patch('/cards/{card}/title', [CardController::class, 'updateTitle'])
            ->middleware('can:update,card');
        Route::patch('/cards/{card}/status', [CardController::class, 'updateStatus'])
            ->middleware('can:update,card');
        Route::patch('/cards/{card}/color', [CardController::class, 'updateColor'])
            ->middleware('can:update,card');
        Route::patch('/cards/{card}/position', [CardController::class, 'updatePosition'])
            ->middleware('can:update,card');
        Route::delete('/cards/{card}', [CardController::class, 'destroy'])
            ->middleware('can:delete,card');
    });
});
