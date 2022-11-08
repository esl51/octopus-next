<?php

use App\Http\Controllers\Access\UserController;
use App\Http\Controllers\AuthController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::put('auth/avatar', [AuthController::class, 'updateAvatar']);
    Route::delete('auth/avatar', [AuthController::class, 'deleteAvatar']);
    Route::get('user/{user}/avatar/{fileName}', [UserController::class, 'avatar'])->name('avatar');
});
