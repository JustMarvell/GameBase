<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;

class PublicGameController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::with(['genre', 'platforms'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->q, fn($query, $q) => $query->where(
                fn($w) => $w->where('title', 'like', "%{$q}%")
                    ->orWhere('developer', 'like', "%{$q}%")
            ))
            ->when($request->genre, fn($query, $id) => $query->where('genre_id', $id))
            ->when($request->platform, fn($query, $id) => $query->whereHas(
                'platforms',
                fn($p) => $p->where('platforms.id', $id)
            ))
            ->when(
                $request->sort,
                fn($query, $sort) => match ($sort) {
                    'rating' => $query->orderByDesc('reviews_avg_rating'),
                    'year' => $query->orderByDesc('release_year'),
                    'title' => $query->orderBy('title'),
                    default => $query->latest(),
                },
                fn($query) => $query->latest()
            )
            ->paginate(12)
            ->withQueryString(); // keeps the filters when changing pages

        return view('games.index', [
            'games' => $games,
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function show(Game $game)
    {
        $game->load(['genre', 'platforms', 'media', 'reviews' => fn($q) => $q->with('user.roles')->latest()]);
        $game->loadAvg('reviews', 'rating');

        $related = Game::with('genre')
            ->withAvg('reviews', 'rating')
            ->where('genre_id', $game->genre_id)
            ->where('id', '!=', $game->id)
            ->take(4)
            ->get();

        $myReview = auth()->check()
            ? $game->reviews->firstWhere('user_id', auth()->id())
            : null;

        return view('games.show', compact('game', 'related', 'myReview'));
    }
}