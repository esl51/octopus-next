<?php

use App\Http\Controllers\FileController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Manage files
    Route::group(['middleware' => ['can:manage files']], function () {
        Route::get('files', [FileController::class, 'index']);
        Route::get('files/{id}', [FileController::class, 'show']);
        Route::put('files/{id}', [FileController::class, 'update']);
        Route::delete('files/{id}', [FileController::class, 'destroy']);
    });
});
