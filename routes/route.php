<?php

use Illuminate\Support\Facades\Route;
use JobMetric\Media\Http\Controllers\MediaController;
use JobMetric\Panelio\Facades\Middleware;

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
    Route::middleware(Middleware::getMiddlewares())->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('new-folder', [MediaController::class, 'newFolder'])->name('new-folder');
        Route::post('upload', [MediaController::class, 'upload'])->name('upload');
        Route::get('download/{media}', [MediaController::class, 'download'])->name('download');
        Route::get('details/{media}', [MediaController::class, 'details'])->name('details');
        Route::post('rename/{media}', [MediaController::class, 'rename'])->name('rename');
        Route::post('delete', [MediaController::class, 'delete'])->name('delete');
        Route::post('restore', [MediaController::class, 'restore'])->name('restore');
        Route::post('compress', [MediaController::class, 'compress'])->name('compress');

        Route::get('image/responsive', [MediaController::class, 'responsive'])->name('image.responsive');
    });
});
