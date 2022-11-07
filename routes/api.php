<?php

use App\Http\Controllers\Access\PermissionController;
use App\Http\Controllers\Access\RoleController;
use App\Http\Controllers\Access\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::put('auth/avatar', [AuthController::class, 'updateAvatar']);
    Route::delete('auth/avatar', [AuthController::class, 'deleteAvatar']);
    Route::get('user/{user}/avatar/{fileName}', [UserController::class, 'avatar'])->name('avatar');

    // Manage access
    Route::group(['middleware' => ['can:manage access']], function () {

        // Permissions
        Route::get('access/permissions', [PermissionController::class, 'index']);
        Route::get('access/permissions/{permission}', [PermissionController::class, 'show']);

        // Permissions
        Route::post('access/permissions', [PermissionController::class, 'store']);
        Route::put('access/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('access/permissions/{permission}', [PermissionController::class, 'destroy']);

        // Roles
        Route::post('access/roles', [RoleController::class, 'store']);
        Route::put('access/roles/{role}', [RoleController::class, 'update']);
        Route::delete('access/roles/{role}', [RoleController::class, 'destroy']);
    });

    // Roles
    Route::get('access/roles', [RoleController::class, 'index']);
    Route::get('access/roles/{role}', [RoleController::class, 'show']);

    // Manage users
    Route::group(['middleware' => ['can:manage users']], function () {

        // Users
        Route::post('access/users', [UserController::class, 'store']);
        Route::put('access/users/{user}', [UserController::class, 'update']);
        Route::delete('access/users/{user}', [UserController::class, 'destroy']);
        Route::get(
            'access/users/{user}/file/{directory?}/{fileName}',
            [
                UserController::class,
                'getFile',
            ]
        )->name('user.file');
        Route::delete('access/users/{user}/file/{directory?}/{fileName}', [UserController::class, 'destroyFile']);
    });

    // Users
    Route::get('access/users', [UserController::class, 'index']);
    Route::get('access/users/{user}', [UserController::class, 'show']);
});
