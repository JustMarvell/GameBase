<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\GameController;

Route::view('/', 'home')->name('home'); // temporary, becomes a controller later

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('genres', GenreController::class)->except('show');
        Route::resource('platforms', PlatformController::class)->except('show');
        Route::resource('games', GameController::class)->except('show');
    });
    