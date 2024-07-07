<?php

use App\Http\Controllers\Files\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('files/{id}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('files/{id}/download', [FileController::class, 'download'])->name('files.download');
});

Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
