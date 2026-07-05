<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookamrkController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('bookmarks')->name('bookmarks.')->group(function () {
    Route::get('/', [BookamrkController::class, 'index'])->name('index');
});

Route::get('/', [BookamrkController::class, 'index'])->name('index');
