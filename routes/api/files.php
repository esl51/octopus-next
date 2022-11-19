<?php

use App\Http\Controllers\FileController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Manage files
    Route::group(['middleware' => ['can:manage files']], function () {
        Route::get('files', [FileController::class, 'index']);
        Route::get('files/{file}', [FileController::class, 'show']);
        Route::put('files/{file}', [FileController::class, 'update']);
        Route::delete('files/{file}', [FileController::class, 'destroy']);
    });
});
