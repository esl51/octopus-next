<?php

use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\PropertyGroupController;
use App\Http\Controllers\Properties\PropertyTypeController;
use App\Http\Controllers\Properties\PropertyValueController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('property-types', PropertyTypeController::class);
    Route::group(['middleware' => ['can:manage properties']], function () {
        // Property Groups
        Route::post('property-groups', [PropertyGroupController::class, 'store']);
        Route::put('property-groups/{id}', [PropertyGroupController::class, 'update']);
        Route::delete('property-groups/{id}', [PropertyGroupController::class, 'destroy']);
        Route::post(
            'property-groups/{id}/move-before/{before}',
            [PropertyGroupController::class, 'moveBefore']
        );
        Route::post(
            'property-groups/{id}/move-after/{after}',
            [PropertyGroupController::class, 'moveAfter']
        );

        // Properties
        Route::post('properties', [PropertyController::class, 'store']);
        Route::put('properties/{id}', [PropertyController::class, 'update']);
        Route::delete('properties/{id}', [PropertyController::class, 'destroy']);

        // Property values
        Route::post('properties/{property}/values', [PropertyValueController::class, 'store']);
        Route::put('properties/{property}/values/{id}', [PropertyValueController::class, 'update']);
        Route::delete('properties/{property}/values/{id}', [PropertyValueController::class, 'destroy']);
        Route::post(
            'properties/{property}/values/{id}/move-before/{before}',
            [PropertyValueController::class, 'moveBefore']
        );
        Route::post(
            'properties/{property}/values/{id}/move-after/{after}',
            [PropertyValueController::class, 'moveAfter']
        );
    });

    // Property Groups
    Route::get('property-groups', [PropertyGroupController::class, 'index']);
    Route::get('property-groups/{id}', [PropertyGroupController::class, 'show']);

    // Properties
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{id}', [PropertyController::class, 'show']);

    // Property values
    Route::get('properties/{property}/values', [PropertyValueController::class, 'index']);
    Route::get('properties/{property}/values/{id}', [PropertyValueController::class, 'show']);
});
