<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
});

Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
