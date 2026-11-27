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
        $games = Game::with(['genres', 'platforms'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->q, fn($query, $q) => $query->where(
                fn($w) => $w->where('title', 'like', "%{$q}%")
                    ->orWhere('developer', 'like', "%{$q}%")
            ))
            ->when($request->genre, fn($query, $id) => $query->whereHas('genres', fn($g) => $g->where('genres.id', $id)))
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
        if (request()->segment(2) !== $game->slug) {
            return redirect()->route('games.show', array_merge(['game' => $game], request()->query()), 301);
        }

        $game->load(['genres', 'platforms', 'media', 'developerAccount', 'reviews' => fn ($q) => $q->with('user.roles')->latest()]);
        $game->loadAvg('reviews', 'rating');

        $related = Game::with('genres')
            ->withAvg('reviews', 'rating')
            ->where('id', '!=', $game->id)
            ->whereHas('genres', fn($q) => $q->whereIn('genres.id', $game->genres->pluck('id')))
            ->take(4)
            ->get();

        $myReview = auth()->check()
            ? $game->reviews->firstWhere('user_id', auth()->id())
            : null;

        return view('games.show', compact('game', 'related', 'myReview'));
    }
}