<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $games = $request->user()->library()
            ->with('genres')
            ->withAvg('reviews', 'rating')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        return view('library.index', compact('games'));
    }

    public function store(Request $request, Game $game)
    {
        $request->user()->library()->syncWithoutDetaching([$game->id]);

        return back()->with('success', "{$game->title} ditambahkan ke library Anda.");
    }

    public function destroy(Request $request, Game $game)
    {
        $request->user()->library()->detach($game->id);

        return back()->with('success', "{$game->title} dihapus dari library Anda.");
    }
}