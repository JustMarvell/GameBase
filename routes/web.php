<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicGameController;
use App\Http\Controllers\PublicGenreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/games', [PublicGameController::class, 'index'])->name('games.index');
Route::get('/games/{game}', [PublicGameController::class, 'show'])->name('games.show');
Route::get('/genres/{genre}', [PublicGenreController::class, 'show'])->name('genres.show');

Route::middleware('auth')->group(function () {
    Route::post('/games/{game}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('genres', GenreController::class)->except('show');
        Route::resource('platforms', PlatformController::class)->except('show');
        Route::resource('games', GameController::class)->except('show');
        Route::resource('reviews', AdminReviewController::class)->only(['index', 'destroy']);
    });
    