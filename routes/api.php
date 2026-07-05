<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookamrkController;
use App\Http\Controllers\Api\NetscapeController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::middleware(['guest', 'throttle:6,1'])->group(function () {
            Route::post('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
        });
        Route::middleware(['auth:api'])->group(function () {
            Route::post('/profile', [AuthController::class, 'profile'])->name('profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });
    });
    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('bookmarks')->name('bookmarks.')->group(function () {
            Route::get('/', [BookamrkController::class, 'index'])->name('index');
            Route::get('/collections', [BookamrkController::class, 'collections'])->name('collections');
            Route::post('/', [BookamrkController::class, 'store'])->name('store');
            Route::get('/{id}', [BookamrkController::class, 'show'])->name('show');
            Route::put('/{id}', [BookamrkController::class, 'update'])->name('update');
            Route::delete('/{id}', [BookamrkController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}', [BookamrkController::class, 'updateAttribute'])->name('updateAttribute');
        });
        Route::prefix('netscape')->name('netscape.')->group(function () {
            Route::post('/import', [NetscapeController::class, 'import'])->name('import');
            Route::get('/export', [NetscapeController::class, 'export'])->name('export');
        });
    });
});
