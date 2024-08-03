<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use JobMetric\Media\Http\Controllers\MediaController;

/*
|--------------------------------------------------------------------------
| Laravel Media Routes
|--------------------------------------------------------------------------
|
| All Route in Laravel Media package
|
*/

// media
Route::prefix('media')->name('media.')->namespace('JobMetric\Media\Http\Controllers')->group(function () {
    Route::middleware([
        SubstituteBindings::class
    ])->group(function () {
        Route::post('upload', [MediaController::class, 'upload'])->name('upload');
        Route::get('download/{media}', [MediaController::class, 'download'])->name('download');
        Route::get('details/{media}', [MediaController::class, 'details'])->name('details');
    });
});
