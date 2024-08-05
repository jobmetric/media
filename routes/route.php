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
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('new-folder', [MediaController::class, 'newFolder'])->name('new-folder');
        Route::post('upload', [MediaController::class, 'upload'])->name('upload');
        Route::get('download/{media}', [MediaController::class, 'download'])->name('download');
        Route::get('details/{media}', [MediaController::class, 'details'])->name('details');
        Route::post('rename/{media}', [MediaController::class, 'rename'])->name('rename');
        Route::post('compress', [MediaController::class, 'compress'])->name('compress');
    });
});
