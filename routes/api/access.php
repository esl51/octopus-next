<?php

use App\Http\Controllers\Access\PermissionController;
use App\Http\Controllers\Access\RoleController;
use App\Http\Controllers\Access\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Manage access
    Route::group(['middleware' => ['can:manage access']], function () {

        // Permissions
        Route::get('access/permissions', [PermissionController::class, 'index']);
        Route::get('access/permissions/{id}', [PermissionController::class, 'show']);
        Route::post('access/permissions', [PermissionController::class, 'store']);
        Route::put('access/permissions/{id}', [PermissionController::class, 'update']);
        Route::delete('access/permissions/{id}', [PermissionController::class, 'destroy']);

        // Roles
        Route::post('access/roles', [RoleController::class, 'store']);
        Route::put('access/roles/{id}', [RoleController::class, 'update']);
        Route::delete('access/roles/{id}', [RoleController::class, 'destroy']);
    });

    // Roles
    Route::get('access/roles', [RoleController::class, 'index']);
    Route::get('access/roles/{id}', [RoleController::class, 'show']);

    // Manage users
    Route::group(['middleware' => ['can:manage users']], function () {

        // Users
        Route::post('access/users', [UserController::class, 'store']);
        Route::put('access/users/{id}', [UserController::class, 'update']);
        Route::delete('access/users/{id}', [UserController::class, 'destroy']);
        Route::post('access/users/{id}/disable', [UserController::class, 'disable']);
        Route::post('access/users/{id}/enable', [UserController::class, 'enable']);
    });

    // Users
    Route::get('access/users', [UserController::class, 'index']);
    Route::get('access/users/{id}', [UserController::class, 'show']);
});
