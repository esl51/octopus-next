<?php

use App\Http\Controllers\AuthController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::put('auth/avatar', [AuthController::class, 'updateAvatar']);
    Route::delete('auth/avatar', [AuthController::class, 'deleteAvatar']);
});
