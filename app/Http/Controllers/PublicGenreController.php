<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class PublicGenreController extends Controller
{
    public function show(Genre $genre)
    {
        $games = $genre->games()
            ->with('genre')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('genres.show', compact('genre', 'games'));
    }
}