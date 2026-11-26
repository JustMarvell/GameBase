<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class PublicGenreController extends Controller
{
    public function show(Genre $genre)
    {
        if (request()->segment(2) !== $genre->slug) {
            return redirect()->route('genres.show', array_merge(['genre' => $genre], request()->query()), 301);
        }
        
        $games = $genre->games()
            ->with('genre')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('genres.show', compact('genre', 'games'));
    }
}