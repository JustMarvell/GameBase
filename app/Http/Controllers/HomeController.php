<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;

class HomeController extends Controller
{
    public function index()
    {
        // Slider: top-rated games (only games that have reviews)
        $slides = Game::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->has('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Fallback so the slider isn't empty before any review exists
        if ($slides->isEmpty()) {
            $slides = Game::with('genres')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->latest()
                ->take(5)
                ->get();
        }

        $latest = Game::with('genres')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->take(8)
            ->get();

        $genres = Genre::withCount('games')->orderBy('name')->get();

        return view('home', compact('slides', 'latest', 'genres'));
    }
}