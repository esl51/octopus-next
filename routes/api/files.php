<?php

use App\Http\Controllers\Files\FileController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Manage files
    Route::group(['middleware' => ['can:manage files']], function () {
        Route::get('files', [FileController::class, 'index']);
        Route::get('files/{id}', [FileController::class, 'show']);
        Route::post('files', [FileController::class, 'store']);
        Route::put('files/{id}', [FileController::class, 'update']);
        Route::delete('files/{id}', [FileController::class, 'destroy']);
        Route::post(
            'files/{id}/move-before/{before}',
            [FileController::class, 'moveBefore']
        );
        Route::post(
            'files/{id}/move-after/{after}',
            [FileController::class, 'moveAfter']
        );
    });
});
