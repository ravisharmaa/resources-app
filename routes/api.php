<?php


use App\Http\Controllers\Api\ResourcesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('resources',
        [ResourcesController::class, 'create'])
        ->name('resources.create');
    Route::put('resources/{resource}',
        [ResourcesController::class, 'update'])
        ->name('resources.update');
    Route::delete('resources/{resource}',
        [ResourcesController::class, 'delete'])
        ->name('resources.delete');
});
